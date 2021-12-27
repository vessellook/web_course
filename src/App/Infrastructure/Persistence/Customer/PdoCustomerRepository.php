<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Customer;

use App\Domain\Customer\Customer;
use App\Domain\Customer\CustomerCanNotBeCreated;
use App\Domain\Customer\CustomerNotFoundException;
use App\Domain\Customer\CustomerRepository;
use Exception;
use PDO;

class PdoCustomerRepository implements CustomerRepository
{

    private static function convertRowToCustomer($row): Customer
    {
        return new Customer(
            id: $row['id'],
            name: $row['name'],
            address: $row['address'],
            phoneNumber: $row['phone_number']
        );
    }

    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @inheritDoc
     * @return Customer[]
     */
    public function findAll(): array
    {
        $this->pdo->query('LOCK TABLES customer READ');
        $stmt = $this->pdo->query('SELECT * FROM customer');
        $this->pdo->query('UNLOCK TABLES');
        $rows = $stmt->fetchAll();
        if (!$rows) {
            return [];
        }
        return array_map(fn($row) => PdoCustomerRepository::convertRowToCustomer($row), $rows);
    }

    /**
     * @throws CustomerNotFoundException
     */
    private function findCustomerById(int $id): Customer
    {
        $stmt = $this->pdo->prepare('SELECT * FROM customer WHERE id = ?');
        $this->pdo->query('LOCK TABLES customer READ');
        $result = $stmt->execute([$id]);
        $this->pdo->query('UNLOCK TABLES');
        if (!$result) {
            throw new CustomerNotFoundException();
        }
        $row = $stmt->fetch();
        if (!$row) {
            throw new CustomerNotFoundException();
        }
        return PdoCustomerRepository::convertRowToCustomer($row);
    }

    /**
     * @inheritDoc
     */
    public function findCustomerOfId(int $id): Customer
    {
        return $this->findCustomerById($id);
    }

    /**
     * @inheritDoc
     */
    public function createCustomer(Customer $customer): Customer
    {
        $stmt = $this->pdo->prepare('INSERT INTO customer (name, address, phone_number) VALUES (?, ?, ?)');
        $stmt->bindValue(1, $customer->getName());
        $stmt->bindValue(2, $customer->getAddress());
        $stmt->bindValue(3, $customer->getPhoneNumber());
        $this->pdo->query('LOCK TABLES customer WRITE');
        $result = $stmt->execute();
        $this->pdo->query('UNLOCK TABLES');
        if (!$result) {
            throw new CustomerCanNotBeCreated();
        }
        $id = intval($this->pdo->lastInsertId());
        $customer->setId($id);
        return $customer;
    }

    public function updateCustomer(Customer $old, Customer $new): Customer
    {
        $this->pdo->query('LOCK TABLES customer WRITE');
        try {
            $realOld = $this->findCustomerById($old->getId());
            if (!$realOld->areSameAttributes($old)) {
                $this->pdo->query('UNLOCK TABLES');
                return $realOld;
            }
            $stmt = $this->pdo->prepare("
UPDATE customer SET name = ?, address = ?, phone_number = ? WHERE id = ?");
            $stmt->bindValue(1, $new->getName());
            $stmt->bindValue(2, $new->getAddress());
            $stmt->bindValue(3, $new->getPhoneNumber());
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

    public function deleteCustomer(int $customerId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM customer WHERE id = ?');
        $this->pdo->query('LOCK TABLES customer WRITE');
        $result = $stmt->execute([$customerId]);
        $this->pdo->query('UNLOCK TABLES');
        return $result;
    }
}
