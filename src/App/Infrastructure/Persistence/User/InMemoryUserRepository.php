<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRegistrationFailureException;
use App\Domain\User\UserRepository;

class InMemoryUserRepository implements UserRepository
{

    private int $counter = 0;

    /**
     * InMemoryUserRepository constructor.
     *
     * @param array $users
     */
    public function __construct(private array $users = [])
    {
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return array_values($this->users);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserOfId(int $id): User
    {
        if (!isset($this->users[$id])) {
            throw new UserNotFoundException();
        }

        return $this->users[$id];
    }

    public function findUserOfLogin(string $login): User
    {
        $userWithLoginArr = array_filter($this->users, fn($user) => $user->login === $login);
        if (!$userWithLoginArr) {
            throw new UserNotFoundException();
        }

        return $userWithLoginArr[0];
    }

    public function registerNewUser(User $user): User
    {
        if (count(array_filter($this->users, fn($user) => $user->getLogin() === $user->getLogin()))) {
            throw new UserRegistrationFailureException();
        }
        $this->counter++;
        $newUser = clone $user;
        $newUser->setId($this->counter);
        $this->users[$this->counter] = $newUser;
        return $newUser;
    }
}
