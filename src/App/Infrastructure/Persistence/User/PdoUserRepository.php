<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRegistrationFailureException;
use App\Domain\User\UserRepository;
use Exception;
use PDO;
use PDOException;
use Psr\Log\LoggerInterface;

class PdoUserRepository implements UserRepository
{
    private static function convertRowToUser(array $row): User
    {
        return new User(
            id: $row['id'],
            role: $row['role'],
            login: $row['login'],
            password: $row['password']
        );
    }


    public function __construct(private PDO $pdo, private LoggerInterface $logger)
    {
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        $this->pdo->query('LOCK TABLES user READ');
        try {
            $stmt = $this->pdo->query('SELECT * FROM user');
            $rows = $stmt->fetchAll();
            return array_map('self::convertRowToUser', $rows);
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
    }

    /**
     * @throws UserNotFoundException
     */
    private function findUserById(int $id): User
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
    public function findUserOfId(int $id): User
    {
        $this->pdo->query('LOCK TABLES user READ');
        try {
            return $this->findUserById($id);
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
    }

    /**
     * @inheritDoc
     */
    public function findUserOfLogin(string $login): User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE login = :login LIMIT 1');
        $stmt->bindValue('login', $login);
        $this->pdo->query('LOCK TABLES user READ');
        try {
            $stmt->execute();
            $row = $stmt->fetch();
            if (!$row) {
                throw new UserNotFoundException();
            }
            return $this->convertRowToUser($row);
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
    }

    /**
     * @inheritDoc
     */
    public function registerNewUser(User $user): User
    {
        $stmt = $this->pdo->prepare('INSERT INTO user (role, login, password) VALUES (?, ?, ?)');
        $stmt->bindValue(1, $user->getRole());
        $stmt->bindValue(2, $user->getLogin());
        $stmt->bindValue(3, $user->getPassword());
        try {
            $this->pdo->query('LOCK TABLES user WRITE');
            if (!$stmt->execute()) {
                throw new UserRegistrationFailureException();
            }
            $id = intval($this->pdo->lastInsertId());
            $user->setId($id);
            return $user;
        } catch (PDOException) {
            throw new UserRegistrationFailureException();
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
    }

    public function updateUser(User $old, User $new): User
    {
        $this->pdo->query('LOCK TABLES user WRITE');
        try {
            $realOld = $this->findUserById($old->getId());
            if (!$realOld->areSameAttributes($old)) {
                return $realOld;
            }
            $stmt = $this->pdo->prepare('UPDATE user SET role = ?, login = ?, password = ? WHERE id = ?');
            $stmt->bindValue(1, $new->getRole());
            $stmt->bindValue(2, $new->getLogin());
            $stmt->bindValue(3, $new->getPassword());
            $stmt->bindValue(4, $old->getId());
            if (!$stmt->execute()) {
                return $old;
            }
            $new->setId($old->getId());
            return $new;
        } catch (Exception) {
            return $old;
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
    }

    public function deleteUser(User $user): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM user WHERE id = ?');
        $this->pdo->query('LOCK TABLES user WRITE');
        try {
            return $stmt->execute([$user->getId()]);
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
    }
}
