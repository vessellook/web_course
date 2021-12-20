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
        private int      $price,
        private int      $count
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

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'uid' => $this->uid,
            'name' => $this->name,
            'price' => $this->price / 100,
            'count' => $this->count
        ];
    }
}
