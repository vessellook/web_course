<?php
declare(strict_types=1);


namespace App\Domain\Order;

class OrderItem implements \JsonSerializable
{

    public function __construct(
        private ?int $id,
        private int $productId,
        private int $count
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

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        // TODO: Implement jsonSerialize() method.
    }
}
