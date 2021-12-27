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
        private string $password
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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function areSameAttributes(User $other): bool
    {
        $a = $this->jsonSerialize();
        $b = $other->jsonSerialize();
        unset($a['id']);
        unset($b['id']);
        return $a == $b && $this->password == $other->password;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'role' => $this->role,
            'login' => $this->login
        ];
    }
}
