<?php
declare(strict_types=1);


namespace App\Domain\Order;

use JsonSerializable;
use \DateTimeImmutable;

class Order implements JsonSerializable
{

    public function __construct(
        private ?int               $id,
        private int                $customerId,
        private int                $productId,
        private string             $address,
        private ?DateTimeImmutable $date,
        private ?string            $agreementCode,
        private ?string            $agreementUrl
    )
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getDate(): ?DateTimeImmutable
    {
        return $this->date;
    }

    public function getAgreementCode(): ?string
    {
        return $this->agreementCode;
    }

    public function getAgreementUrl(): ?string
    {
        return $this->agreementUrl;
    }

    public function areSameAttributes(Order $other): bool
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
            'customerId' => $this->customerId,
            'productId' => $this->productId,
            'address' => $this->address,
            'date' => $this->date?->format('Y-m-d'),
            'agreementCode' => $this->agreementCode,
            'agreementUrl' => $this->agreementUrl
        ];
    }
}
