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
        $stmt = $this->pdo->query('SELECT * FROM user');
        $this->pdo->query('UNLOCK TABLES');
        $rows = $stmt->fetchAll();
        return array_map('self::convertRowToUser', $rows);
    }

    /**
     * @throws UserNotFoundException
     */
    private function findUserById(int $id, bool $forUpdate = false): User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE id = :id' . ($forUpdate ? ' FOR UPDATE' : ''));
        $stmt->bindValue('id', $id);
        $this->pdo->query('LOCK TABLES user READ');
        $stmt->execute();
        $this->pdo->query('UNLOCK TABLES');
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
        return $this->findUserById($id);
    }

    /**
     * @inheritDoc
     */
    public function findUserOfLogin(string $login): User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE login = :login LIMIT 1');
        $stmt->bindValue('login', $login);
        $this->pdo->query('LOCK TABLES user READ');
        $stmt->execute();
        $this->pdo->query('UNLOCK TABLES');
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
        $stmt = $this->pdo->prepare('INSERT INTO user (role, login, password) VALUES (?, ?, ?)');
        $stmt->bindValue(1, $user->getRole());
        $stmt->bindValue(2, $user->getLogin());
        $stmt->bindValue(3, $user->getPassword());
        try {
            $this->pdo->query('LOCK TABLES user WRITE');
            $result = $stmt->execute();
            $this->pdo->query('UNLOCK TABLES');
            if (!$result) {
                throw new UserRegistrationFailureException();
            }
        } catch (PDOException) {
            throw new UserRegistrationFailureException();
        }
        $id = intval($this->pdo->lastInsertId());
        $user->setId($id);
        return $user;
    }

    public function updateUser(User $old, User $new): User
    {
        $this->pdo->query('LOCK TABLES user WRITE');
        try {
            $realOld = $this->findUserById($old->getId(), forUpdate: true);
            if (!$realOld->areSameAttributes($old)) {
                $this->pdo->query('UNLOCK TABLES');
                return $realOld;
            }
            $stmt = $this->pdo->prepare('UPDATE user SET role = ?, login = ?, password = ? WHERE id = ?');
            $stmt->bindValue(1, $new->getRole());
            $stmt->bindValue(2, $new->getLogin());
            $stmt->bindValue(3, $new->getPassword());
            $stmt->bindValue(4, $old->getId());
            $result = $stmt->execute();
            $this->pdo->query('UNLOCK TABLES');
            if (!$result) {
                return $old;
            }
            $new->setId($old->getId());
            return $new;
        } catch (Exception) {
            $this->pdo->query('UNLOCK TABLES');
            return $old;
        }
    }

    public function deleteUser(User $user): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM user WHERE id = ?');
        $this->pdo->query('LOCK TABLES user WRITE');
        $result = $stmt->execute([$user->getId()]);
        $this->pdo->query('UNLOCK TABLES');
        return $result;
    }
}
