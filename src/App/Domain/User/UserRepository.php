<?php
declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{
    /**
     * @return User[]
     */
    public function findAll(): array;

    /**
     * @throws UserNotFoundException
     */
    public function findUserOfId(int $id): User;

    /**
     * @throws UserNotFoundException
     */
    public function findUserOfLogin(string $login): User;

    /**
     * @throws UserRegistrationFailureException
     */
    public function registerNewUser(User $user): User;

    public function updateUser(User $old, User $new): User;

    public function deleteUser(User $user): bool;
}
