<?php
declare(strict_types=1);


namespace App\Domain\Customer;

use JsonSerializable;

class Customer implements JsonSerializable
{

    public function __construct(
        private ?int    $id,
        private string  $name,
        private ?string $address,
        private ?string $phoneNumber
    )
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function areSameAttributes(Customer $other): bool
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
            'name' => $this->name,
            'address' => $this->address,
            'phoneNumber' => $this->phoneNumber
        ];
    }
}
