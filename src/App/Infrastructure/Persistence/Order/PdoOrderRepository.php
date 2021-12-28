<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Order;

use App\Domain\DomainException\DomainRecordCreationFailureException;
use App\Domain\Order\Order;
use App\Domain\Order\OrderNotFoundException;
use App\Domain\Order\OrderRepository;
use App\Domain\Transportation\Transportation;
use Assert\AssertionFailedException;
use PDO;
use DateTimeImmutable;
use Exception;
use Psr\Log\LoggerInterface;

class PdoOrderRepository implements OrderRepository
{

    public function __construct(private PDO $pdo, private LoggerInterface $logger)
    {
    }

    private static function convertRowToOrder($row): Order
    {
        $date = null;
        if (isset($row['date'])) {
            $date = DateTimeImmutable::createFromFormat('Y-m-d', $row['date']);
        }
        return new Order(
            id: $row['id'],
            customerId: $row['customer_id'],
            productId: $row['product_id'],
            address: $row['address'],
            date: $date,
            agreementCode: $row['agreement_code'],
            agreementUrl: $row['agreement_url']
        );
    }

    /**
     * @inheritDoc
     * @return Order[]
     */
    public function findAll(): array
    {
        $this->pdo->query('LOCK TABLES `order` READ');
        try {
            $this->logger->info('1');
            $stmt = $this->pdo->query('SELECT * FROM `order`');
            $rows = $stmt->fetchAll();
            if (!$rows) {
                return [];
            }
            return array_map('self::convertRowToOrder', $rows);
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
    }

    /**
     * @inheritDoc
     * @return Order[]
     */
    public function findAllOfCustomer(int $customerId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `order` WHERE customer_id = ?');
        $this->pdo->query('LOCK TABLES `order` READ');
        try {
            $stmt->execute([$customerId]);
            $rows = $stmt->fetchAll();
            if (!$rows) {
                return [];
            }
            return array_map('self::convertRowToOrder', $rows);
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
    }

    /**
     * @throws OrderNotFoundException
     */
    private function findOrderById(int $id): Order
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `order` WHERE id = ?');
        $this->pdo->query('LOCK TABLES `order` READ');
        try {
            if (!$stmt->execute([$id])) {
                throw new OrderNotFoundException();
            }
            $row = $stmt->fetch();
            if (!$row) {
                throw new OrderNotFoundException();
            }
            return self::convertRowToOrder($row);
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
    }

    /**
     * @inheritDoc
     */
    public function findOrderOfId(int $id): Order
    {
        return $this->findOrderById($id);
    }

    /**
     * @inheritDoc
     */
    public function findOrderWithTransportationsOfId(int $id): array
    {
        $this->pdo->query('LOCK TABLES `order` as o READ, transportation as t READ');
        $stmt = $this->pdo->prepare('
SELECT o.*, t.id as t_id, t.planned_date, t.real_date, t.number, t.status 
FROM `order` o LEFT JOIN transportation t on o.id = t.order_id 
WHERE o.id = ?');
        try {
            if (!$stmt->execute([$id])) {
                throw new OrderNotFoundException();
            }
            $rows = $stmt->fetchAll();
            if (!$rows) {
                throw new OrderNotFoundException();
            }
            $order = self::convertRowToOrder($rows[0]);
            if (!$rows[0]['t_id']) {
                return ['order' => $order, 'transportations' => []];
            }
            return ['order' => $order, 'transportations' => array_map(function ($row) {
                $plannedDate = DateTimeImmutable::createFromFormat('Y-m-d', $row['planned_date']);

                $realDate = null;
                if (isset($row['real_date'])) {
                    $realDate = DateTimeImmutable::createFromFormat('Y-m-d', $row['real_date']);
                }
                return new Transportation(
                    id: $row['t_id'],
                    orderId: $row['id'],
                    plannedDate: $plannedDate,
                    realDate: $realDate,
                    number: $row['number'],
                    status: $row['status']
                );
            }, $rows)];
        } catch (AssertionFailedException $e) {
            throw new OrderNotFoundException();
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
    }

    /**
     * @inheritDoc
     */
    public function createOrder(Order $order): Order
    {
        $stmt = $this->pdo->prepare('
INSERT INTO `order` (product_id, customer_id, address, date, agreement_code, agreement_url)
VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bindValue(1, $order->getProductId());
        $stmt->bindValue(2, $order->getCustomerId());
        $stmt->bindValue(3, $order->getAddress());
        $stmt->bindValue(4, $order->getDate()?->format('Y-m-d'));
        $stmt->bindValue(5, $order->getAgreementCode());
        $stmt->bindValue(6, $order->getAgreementUrl());
        $this->pdo->query('LOCK TABLES `order` WRITE');
        try {
            if (!$stmt->execute()) {
                throw new DomainRecordCreationFailureException();
            }
            $orderId = intval($this->pdo->lastInsertId());
            $order->setId($orderId);
            return $order;
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
    }

    /**
     * @inheritDocs
     * @param Transportation[] $transportations
     */
    public function createOrderWithTransportations(Order $order, array $transportations): Order
    {
        $stmt = $this->pdo->prepare('
INSERT INTO `order` (product_id, customer_id, address, date, agreement_code, agreement_url)
VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bindValue(1, $order->getProductId());
        $stmt->bindValue(2, $order->getCustomerId());
        $stmt->bindValue(3, $order->getAddress());
        $stmt->bindValue(4, $order->getDate()?->format('Y-m-d'));
        $stmt->bindValue(5, $order->getAgreementCode());
        $stmt->bindValue(6, $order->getAgreementUrl());
        $this->pdo->query('LOCK TABLES `order` WRITE, transportation WRITE');
        try {
            if (!$stmt->execute()) {
                throw new DomainRecordCreationFailureException();
            }
            $orderId = intval($this->pdo->lastInsertId());
            $order->setId($orderId);
            foreach ($transportations as $transportation) {
                $stmt = $this->pdo->prepare(
                    'INSERT INTO transportation (order_id, planned_date, real_date, number, status) 
VALUES (?, ?, ?, ?, ?)'
                );
                $stmt->bindValue(1, $order->getId());
                $stmt->bindValue(2, $transportation->getPlannedDate()->format('Y-m-d'));
                $stmt->bindValue(3, $transportation->getRealDate()?->format('Y-m-d'));
                $stmt->bindValue(4, $transportation->getNumber());
                $stmt->bindValue(5, $transportation->getStatus());
                $stmt->execute();
            }
            return $order;
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
    }

    public function updateOrder(Order $old, Order $new): Order
    {
        $this->pdo->query('LOCK TABLES `order` WRITE');
        try {
            $realOld = $this->findOrderById($old->getId());
            if (!$realOld->areSameAttributes($old)) {
                return $realOld;
            }
            $stmt = $this->pdo->prepare('
UPDATE `order`
SET customer_id = ?, product_id = ?, address = ?, date = ?, agreement_code = ?, agreement_url = ?
WHERE id = ?');
            $stmt->bindValue(1, $new->getCustomerId());
            $stmt->bindValue(2, $new->getProductId());
            $stmt->bindValue(3, $new->getAddress());
            $stmt->bindValue(4, $new->getDate()?->format('Y-m-d'));
            $stmt->bindValue(5, $new->getAgreementCode());
            $stmt->bindValue(6, $new->getAgreementUrl());
            $stmt->bindValue(7, $old->getId());
            if (!$stmt->execute()) {
                return $old;
            }
            $new->setId($old->getId());
            return $new;
        } catch (Exception) {
            return $old;
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
    }

    public function deleteOrder(int $orderId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM `order` WHERE id = ?');
        $this->pdo->query('LOCK TABLES `order` WRITE');
        try {
            return $stmt->execute([$orderId]);
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
    }
}
