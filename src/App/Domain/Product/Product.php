<?php
declare(strict_types=1);

namespace App\Domain\Product;

use JsonSerializable;

class Product implements JsonSerializable
{
    public function __construct(
        private null|int $id,
        private string   $uid,
        private string   $name,
        private int      $price
    ) {
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    public function areSameAttributes(Product $other): bool
    {
        $a = $this->jsonSerialize();
        $b = $other->jsonSerialize();
        unset($a['id']);
        unset($b['id']);
        return $a == $b;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'uid' => $this->uid,
            'name' => $this->name,
            'price' => $this->price / 100
        ];
    }
}
