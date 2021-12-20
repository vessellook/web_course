<?php
declare(strict_types=1);

namespace App\Domain\User;

use JsonSerializable;

class User implements JsonSerializable
{
    public function __construct(
        private ?int   $id,
        private string $role,
        private string $login,
        private string $email,
        private ?string $phoneNumber,
        private string $passwordHash,
        private string $name
    ) {
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'role' => $this->role,
            'login' => $this->login,
            'email' => $this->email,
            'phoneNumber' => $this->phoneNumber,
            'name' => $this->name
        ];
    }
}
