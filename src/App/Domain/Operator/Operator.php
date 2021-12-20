<?php
declare(strict_types=1);


namespace App\Domain\Operator;

use JsonSerializable;

class Operator implements JsonSerializable
{
    public function __construct(
        private int     $id,
        private string  $login,
        private string  $email,
        private ?string $phoneNumber,
        private string  $name
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function getRole(): string
    {
        return 'operator';
    }

    public function jsonSerialize(): array
    {
        return [
          'id' => $this->id,
          'role' => 'operator',
          'login' => $this->login,
          'email' => $this->email,
          'phoneNumber' => $this->phoneNumber,
          'name' => $this->name,
        ];
    }
}
