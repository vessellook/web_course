<?php
declare(strict_types=1);

namespace App\Domain\Order;

use App\Domain\DomainException\DomainRecordCreationFailureException;
use App\Domain\Transportation\Transportation;

interface OrderRepository
{
    /**
     * @return Order[]
     */
    public function findAll(): array;

    /**
     * @return Order[]
     */
    public function findAllOfCustomer(int $customerId): array;

    /**
     * @param int $id
     * @return Order
     * @throws OrderNotFoundException
     */
    public function findOrderOfId(int $id): Order;

    /**
     * @param int $id
     * @return array{order: Order, transportations: array}
     * @throws OrderNotFoundException
     */
    public function findOrderWithTransportationsOfId(int $id): array;

    /**
     * @param Order $order
     * @return Order
     * @throws DomainRecordCreationFailureException
     */
    public function createOrder(Order $order): Order;

    /**
     * @param Order $order
     * @param Transportation[] $transportations
     * @return Order
     * @throws DomainRecordCreationFailureException
     */
    public function createOrderWithTransportations(Order $order, array $transportations): Order;

    public function updateOrder(Order $old, Order $new): Order;

    public function deleteOrder(int $orderId): bool;
}