<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRegistrationFailureException;
use App\Domain\User\UserRepository;
use PDO;

class PdoUserRepository implements UserRepository
{
    private function convertRowToUser(array $row): User
    {
        return new User(
            $row['id'],
            $row['role'],
            $row['login'],
            $row['email'],
            $row['phone_number'],
            $row['password_hash'],
            $row['name'],
        );
    }


    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM user');
        $rows = $stmt->fetchAll();
        return array_map([$this, 'convertRowToUser'], $rows);
    }

    /**
     * @inheritDoc
     */
    public function findUserOfId(int $id): User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE id = :id');
        $stmt->bindValue('id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
        if (!$row) {
            throw new UserNotFoundException();
        }
        return $this->convertRowToUser($row);
    }

    /**
     * @inheritDoc
     */
    public function findUserOfLogin(string $login): User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE login = :login LIMIT 1');
        $stmt->bindValue('login', $login);
        $stmt->execute();
        $row = $stmt->fetch();
        if (!$row) {
            throw new UserNotFoundException();
        }
        return $this->convertRowToUser($row);
    }

    /**
     * @inheritDoc
     */
    public function registerNewUser(User $user): User
    {
        $stmt = $this->pdo->prepare('INSERT INTO user (role, login, email, phone_number, password_hash, name)
 VALUES (:role, :login, :email, :phone_number, :password_hash, :name)');
        $stmt->bindValue('role', $user->getRole());
        $stmt->bindValue('login', $user->getLogin());
        $stmt->bindValue('email', $user->getEmail());
        $stmt->bindValue('phone_number', $user->getPhoneNumber());
        $stmt->bindValue('password_hash', $user->getPasswordHash());
        $stmt->bindValue('name', $user->getName());
        if (!$stmt->execute()) {
            throw new UserRegistrationFailureException();
        }
        $id = intval($this->pdo->lastInsertId());
        $user->setId($id);
        return $user;
    }
}
