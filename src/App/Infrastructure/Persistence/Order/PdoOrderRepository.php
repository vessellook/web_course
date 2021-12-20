<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Order;

use App\Domain\DomainException\DomainRecordCreationFailureException;
use App\Domain\DomainException\DomainRecordDeletionFailedException;
use App\Domain\Order\Order;
use App\Domain\Order\OrderItem;
use App\Domain\Order\OrderNotFoundException;
use App\Domain\Order\OrderRepository;
use PDO;

class PdoOrderRepository implements OrderRepository
{

    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @return Order[]
     */
    private function getOrdersFromRows($rows): array
    {
        $orderData = [];
        foreach ($rows as $row) {
            if (!key_exists($row['order_id'], $orderData)) {
                $orderData[$row['order_id']] = [
                    'id' => $row['order_id'],
                    'address' => $row['address'],
                    'status' => $row['status'],
                    'price' => $row['price'],
                    'userId' => $row['user_id'],
                    'items' => []
                ];
            }
            if (isset($row['item_id'])) {
                $item = new OrderItem(id: $row['item_id'], productId: $row['product_id'], count: $row['count']);
                $orderData[$row['order_id']]['items'][] = $item;
            }
        }
        return array_map(fn($data) => new Order(
            id: $data['id'],
            status: $data['status'],
            address: $data['address'],
            price: $data['price'],
            items: $data['items'],
            userId: $data['userId']
        ), $orderData);
    }

    /**
     * @inheritDoc
     */
    public function findAllOrdersOfUser(int $userId): array
    {
        $stmt = $this->pdo->prepare('
SELECT o.id as order_id, user_id, address, status, price, op.id as item_id, count, product_id
FROM `order` o LEFT JOIN order_product op on o.id = op.order_id
WHERE user_id = :user_id');
        $stmt->bindValue('user_id', $userId);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        return $this->getOrdersFromRows($rows);
    }

    /**
     * @inheritDoc
     */
    public function findOrderOfId(int $id): Order
    {
        $stmt = $this->pdo->prepare('
SELECT o.id as order_id, user_id, address, status, price, op.id as item_id, count, product_id
FROM `order` o LEFT JOIN order_product op on o.id = op.order_id
WHERE o.id = :id');
        $stmt->bindValue('id', $id);
        if (!$stmt->execute()) {
            throw new OrderNotFoundException();
        }
        $rows = $stmt->fetchAll();
        $orders = $this->getOrdersFromRows($rows);
        return $orders[0];
    }

    public function createOrder(Order $order): Order
    {
        $stmt = $this->pdo->prepare('INSERT INTO `order` (user_id, address, status, price)
VALUES (:user_id, :address, :status, :price) ON DUPLICATE KEY UPDATE id=id');
        $stmt->bindValue('user_id', $order->getUserId());
        $stmt->bindValue('address', $order->getAddress());
        $stmt->bindValue('status', $order->getStatus());
        $stmt->bindValue('price', $order->getPrice());
        if (!$stmt->execute()) {
            throw new DomainRecordCreationFailureException();
        }
        $orderId = intval($this->pdo->lastInsertId());
        $order->setId($orderId);
        foreach ($order->getItems() as $item) {
            $stmt = $this->pdo->prepare('INSERT INTO order_product (product_id, order_id, count)
VALUES (:product_id, :order_id, :count) ON DUPLICATE KEY UPDATE id=id');
            $stmt->bindValue('product_id', $item->getProductId());
            $stmt->bindValue('order_id', $orderId);
            $stmt->bindValue('count', $item->getCount());
            if (!$stmt->execute()) {
                throw new DomainRecordCreationFailureException();
            }
            $itemId = intval($this->pdo->lastInsertId());
            $item->setId($itemId);
        }
        return $order;
    }

    public function removeItemFromOrder(OrderItem $item, Order $order): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM order_product WHERE id = :id AND order_id = :order_id');
        $stmt->bindValue('id', $item->getId());
        $stmt->bindValue('order_id', $order->getId());
        if (!$stmt->execute()) {
            throw new DomainRecordDeletionFailedException();
        }
    }

    public function addItemToOrder(OrderItem $item, Order $order): OrderItem
    {
        $stmt = $this->pdo->prepare('INSERT INTO order_product (product_id, order_id, count)
VALUES (:product_id, :order_id, :count) ON DUPLICATE KEY UPDATE id=id');
        $stmt->bindValue('product_id', $item->getProductId());
        $stmt->bindValue('order_id', $order->getId());
        $stmt->bindValue('count', $item->getCount());
        if (!$stmt->execute()) {
            throw new DomainRecordCreationFailureException();
        }
        $itemId = intval($this->pdo->lastInsertId());
        $item->setId($itemId);
        return $item;
    }
}
