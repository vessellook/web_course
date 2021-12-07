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
        private string $phone_number,
        private string $password_hash,
        private string $name)
    {
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

    public function getPhoneNumber(): string
    {
        return $this->phone_number;
    }

    public function getPasswordHash(): string
    {
        return $this->password_hash;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
        ];
    }
}
