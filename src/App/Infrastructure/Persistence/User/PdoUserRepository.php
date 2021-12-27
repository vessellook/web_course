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
        $stmt = $this->pdo->query('SELECT * FROM user');
        $rows = $stmt->fetchAll();
        return array_map('self::convertRowToUser', $rows);
    }

    private function findUserById(int $id, bool $forUpdate = false): User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE id = :id' . ($forUpdate ? ' FOR UPDATE' : ''));
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
        return $this->findUserById($id);
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
        $stmt = $this->pdo->prepare('INSERT INTO user (role, login, password) VALUES (?, ?, ?)');
        $stmt->bindValue(1, $user->getRole());
        $stmt->bindValue(2, $user->getLogin());
        $stmt->bindValue(3, $user->getPassword());
        try {
            if (!$stmt->execute()) {
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
        $this->pdo->beginTransaction();
        try {
            $realOld = $this->findUserById($old->getId(), forUpdate: true);
            if (!$realOld->areSameAttributes($old)) {
                $this->pdo->rollBack();
                return $realOld;
            }
            $stmt = $this->pdo->prepare('UPDATE user SET role = ?, login = ?, password = ? WHERE id = ?');
            $stmt->bindValue(1, $new->getRole());
            $stmt->bindValue(2, $new->getLogin());
            $stmt->bindValue(3, $new->getPassword());
            $stmt->bindValue(4, $old->getId());
            if (!$stmt->execute()) {
                $this->pdo->rollBack();
                return $old;
            }
            $this->pdo->commit();
            $new->setId($old->getId());
            return $new;
        } catch (Exception) {
            $this->pdo->rollBack();
            return $old;
        }
    }

    public function deleteUser(User $user): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM user WHERE id = ?');
        return $stmt->execute([$user->getId()]);
    }
}
