<?php
declare(strict_types=1);

namespace App\Domain\Order;

use App\Domain\DomainException\DomainException;
use App\Domain\DomainException\DomainRecordCreationFailureException;

interface OrderRepository
{
    /**
     * @return Order[]
     */
    public function findAllOrdersOfUser(int $userId): array;

    /**
     * @param int $id
     * @return Order
     * @throws OrderNotFoundException
     */
    public function findOrderOfId(int $id): Order;

    /**
     * @param Order $order
     * @return Order
     * @throws DomainRecordCreationFailureException
     */
    public function createOrder(Order $order): Order;

    /**
     * @param Order $order
     * @param OrderItem $item
     * @throws DomainException
     */
    public function removeItemFromOrder(OrderItem $item, Order $order): void;

    /**
     * @param OrderItem $item
     * @param Order $order
     * @return OrderItem
     * @throws DomainException
     */
    public function addItemToOrder(OrderItem $item, Order $order): OrderItem;
}