<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Product;

use \Exception;
use App\Domain\Product\Product;
use App\Domain\Product\ProductCreationFailureException;
use App\Domain\Product\ProductNotFoundException;
use App\Domain\Product\ProductRepository;
use PDO;
use Psr\Log\LoggerInterface;

class PdoProductRepository implements ProductRepository
{

    private static function convertRowToProduct(array $row): Product
    {
        return new Product(
            id: $row['id'],
            uid: $row['uid'],
            name: $row['name'],
            price: $row['price']
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
        $this->pdo->query('LOCK TABLES product READ');
        $stmt = $this->pdo->query('SELECT * FROM product');
        $this->pdo->query('UNLOCK TABLES');
        if (!$stmt) {
            $this->logger->error('error in statement', ['line' => __LINE__, 'file' => __FILE__]);
            return [];
        }
        return array_map('self::convertRowToProduct', $stmt->fetchAll());
    }

    /**
     * @throws ProductNotFoundException
     */
    private function findProductById(int $id): Product
    {
        $this->pdo->query('LOCK TABLES product READ');
        $stmt = $this->pdo->query("SELECT * FROM product WHERE id = $id");
        $this->pdo->query('UNLOCK TABLES');
        if (!$stmt) {
            $this->logger->error('error in statement', ['line' => __LINE__, 'file' => __FILE__]);
            throw new ProductNotFoundException();
        }
        $row = $stmt->fetch();
        return PdoProductRepository::convertRowToProduct($row);
    }

    /**
     * @inheritDoc
     */
    public function findProductOfId(int $id): Product
    {
        return $this->findProductById($id);
    }

    /**
     * @inheritDoc
     */
    public function findProductOfUid(string $uid): Product
    {
        $stmt = $this->pdo->prepare("SELECT * FROM product WHERE uid = ?");
        $this->pdo->query('LOCK TABLES product READ');
        $result = $stmt->execute([$uid]);
        $this->pdo->query('UNLOCK TABLES');
        if (!$result) {
            $this->logger->error(
                'error in statement ' . $stmt->queryString,
                ['line' => __LINE__, 'file' => __FILE__]
            );
            throw new ProductNotFoundException();
        }
        $row = $stmt->fetch();
        return PdoProductRepository::convertRowToProduct($row);
    }

    public function createProduct(Product $product): Product
    {
        $stmt = 'INSERT INTO product (uid, name, price) VALUES (:uid, :name, :price)';
        $stmt = $this->pdo->prepare($stmt);
        $stmt->bindValue('uid', $product->getUid());
        $stmt->bindValue('name', $product->getName());
        $stmt->bindValue('price', $product->getPrice());
        $this->pdo->query('LOCK TABLES product WRITE');
        $result = $stmt->execute();
        $this->pdo->query('UNLOCK TABLES');
        if (!$result) {
            throw new ProductCreationFailureException();
        }
        $id = intval($this->pdo->lastInsertId());
        $stmt = $this->pdo->query("SELECT * FROM product WHERE id = $id");
        $row = $stmt->fetch();
        return PdoProductRepository::convertRowToProduct($row);
    }

    public function updateProduct(Product $old, Product $new): Product
    {
        $this->pdo->query('LOCK TABLES product WRITE');
        try {
            $realOld = $this->findProductById($old->getId());
            if (!$realOld->areSameAttributes($old)) {
                $this->pdo->query('UNLOCK TABLES');
                return $realOld;
            }
            $stmt = $this->pdo->query('UPDATE product SET uid = ?, name = ?, price = ? WHERE id = ?');
            $stmt->bindValue(1, $new->getUid());
            $stmt->bindValue(2, $new->getName());
            $stmt->bindValue(3, $new->getPrice());
            $stmt->bindValue(3, $old->getId());
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

    public function deleteProduct(Product $product): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM product WHERE id = ?');
        $this->pdo->query('LOCK TABLES product WRITE');
        $result = $stmt->execute([$product->getId()]);
        $this->pdo->query('UNLOCK TABLES');
        return $result;
    }
}
