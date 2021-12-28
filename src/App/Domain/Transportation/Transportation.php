<?php
declare(strict_types=1);


namespace App\Domain\Transportation;

use Assert\Assertion;
use Assert\AssertionFailedException;
use JsonSerializable;
use DateTimeImmutable;

class Transportation implements JsonSerializable
{

    /**
     * @throws AssertionFailedException
     */
    public function __construct(
        private ?int               $id,
        private ?int               $orderId,
        private DateTimeImmutable $plannedDate,
        private ?DateTimeImmutable $realDate,
        private int               $number,
        private string            $status
    ) {
        if ($status === 'finished') {
            Assertion::notBlank($realDate);
        }
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
     * @return int|null $orderId
     */
    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getPlannedDate(): DateTimeImmutable
    {
        return $this->plannedDate;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getRealDate(): ?DateTimeImmutable
    {
        return $this->realDate;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    public function areSameAttributes(Transportation $other): bool
    {
        $a = $this->jsonSerialize();
        $b = $other->jsonSerialize();
        unset($a['id']);
        unset($b['id']);
        return $a == $b;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'orderId' => $this->orderId,
            'plannedDate' => $this->plannedDate->format('Y-m-d'),
            'realDate' => $this->realDate?->format('Y-m-d'),
            'number' => $this->number,
            'status' => $this->status
        ];
    }
}
