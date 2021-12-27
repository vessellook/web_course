<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Order;

use App\Domain\DomainException\DomainRecordCreationFailureException;
use App\Domain\Order\Order;
use App\Domain\Order\OrderNotFoundException;
use App\Domain\Order\OrderRepository;
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
        $this->pdo->query('LOCK TABLES order READ');
        $stmt = $this->pdo->query('SELECT * FROM `order`');
        $this->pdo->query('UNLOCK TABLES');
        $rows = $stmt->fetchAll();
        if (!$rows) {
            return [];
        }
        return array_map('self::convertRowToOrder', $rows);
    }

    /**
     * @inheritDoc
     * @return Order[]
     */
    public function findAllOfCustomer(int $customerId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `order` WHERE customer_id = ?');
        $this->pdo->query('LOCK TABLES order READ');
        $stmt->execute([$customerId]);
        $this->pdo->query('UNLOCK TABLES');
        $rows = $stmt->fetchAll();
        if (!$rows) {
            return [];
        }
        return array_map('self::convertRowToOrder', $rows);
    }

    /**
     * @throws OrderNotFoundException
     */
    private function findOrderById(int $id, bool $forUpdate = false): Order
    {
        $this->pdo->query('LOCK TABLES order READ');
        $stmt = $this->pdo->prepare('SELECT * FROM `order` WHERE id = ?' . ($forUpdate ? ' FOR UPDATE' : ''));
        $result = $stmt->execute([$id]);
        $this->pdo->query('UNLOCK TABLES');
        if (!$result) {
            throw new OrderNotFoundException();
        }
        $row = $stmt->fetch();
        if (!$row) {
            throw new OrderNotFoundException();
        }
        return self::convertRowToOrder($row);
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
        $this->pdo->query('LOCK TABLES order WRITE');
        $result = $stmt->execute();
        $this->pdo->query('UNLOCK TABLES');
        if (!$result) {
            throw new DomainRecordCreationFailureException();
        }
        $orderId = intval($this->pdo->lastInsertId());
        $order->setId($orderId);
        return $order;
    }

    public function updateOrder(Order $old, Order $new): Order
    {
        $this->pdo->query('LOCK TABLES order WRITE');
        try {
            $realOld = $this->findOrderById($old->getId(), forUpdate: true);
            if (!$realOld->areSameAttributes($old)) {
                $this->pdo->query('UNLOCK TABLES');
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
            $result = $stmt->execute();
            $this->pdo->query('UNLOCK TABLES');
            if (!$result) {
                return $old;
            }
            $new->setId($old->getId());
            return $new;
        } catch (Exception) {
            $this->pdo->query('UNLOCK TABLES');
            return $old;
        }
    }

    public function deleteOrder(int $orderId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM `order` WHERE id = ?');
        $this->pdo->query('LOCK TABLES order WRITE');
        $result = $stmt->execute([$orderId]);
        $this->pdo->query('UNLOCK TABLES');
        return $result;
    }
}
