<?php
declare(strict_types=1);


namespace App\Domain\Order;

use JsonSerializable;

class Order implements JsonSerializable
{

    /** @param OrderItem[] $items */
    public function __construct(
        private ?int $id,
        private string $status,
        private ?int $price,
        private string $address,
        private array $items,
        private int $userId
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * @return OrderItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'status' => $this->status,
            'price' => $this->price,
            'items' => $this->items
        ];
    }
}
