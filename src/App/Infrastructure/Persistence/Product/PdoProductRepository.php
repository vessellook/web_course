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
        try {
            $stmt = $this->pdo->query('SELECT * FROM product');
            if (!$stmt) {
                $this->logger->error('error in statement', ['line' => __LINE__, 'file' => __FILE__]);
                return [];
            }
            return array_map('self::convertRowToProduct', $stmt->fetchAll());
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
    }

    /**
     * @throws ProductNotFoundException
     */
    private function findProductById(int $id): Product
    {
        $this->pdo->query('LOCK TABLES product READ');
        try {
            $stmt = $this->pdo->query("SELECT * FROM product WHERE id = $id");
            if (!$stmt) {
                $this->logger->error('error in statement', ['line' => __LINE__, 'file' => __FILE__]);
                throw new ProductNotFoundException();
            }
            $row = $stmt->fetch();
            return PdoProductRepository::convertRowToProduct($row);
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
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
        try {
            if (!$stmt->execute([$uid])) {
                $this->logger->error(
                    'error in statement ' . $stmt->queryString,
                    ['line' => __LINE__, 'file' => __FILE__]
                );
                throw new ProductNotFoundException();
            }
            $row = $stmt->fetch();
            return PdoProductRepository::convertRowToProduct($row);
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
    }

    public function createProduct(Product $product): Product
    {
        $stmt = 'INSERT INTO product (uid, name, price) VALUES (:uid, :name, :price)';
        $stmt = $this->pdo->prepare($stmt);
        $stmt->bindValue('uid', $product->getUid());
        $stmt->bindValue('name', $product->getName());
        $stmt->bindValue('price', $product->getPrice());
        $this->pdo->query('LOCK TABLES product WRITE');
        try {
            if (!$stmt->execute()) {
                throw new ProductCreationFailureException();
            }
            $id = intval($this->pdo->lastInsertId());
            $stmt = $this->pdo->query("SELECT * FROM product WHERE id = $id");
            $row = $stmt->fetch();
            return PdoProductRepository::convertRowToProduct($row);
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
    }

    public function updateProduct(Product $old, Product $new): Product
    {
        $this->pdo->query('LOCK TABLES product WRITE');
        try {
            $realOld = $this->findProductById($old->getId());
            if (!$realOld->areSameAttributes($old)) {
                return $realOld;
            }
            $stmt = $this->pdo->query('UPDATE product SET uid = ?, name = ?, price = ? WHERE id = ?');
            $stmt->bindValue(1, $new->getUid());
            $stmt->bindValue(2, $new->getName());
            $stmt->bindValue(3, $new->getPrice());
            $stmt->bindValue(3, $old->getId());
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

    public function deleteProduct(Product $product): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM product WHERE id = ?');
        $this->pdo->query('LOCK TABLES product WRITE');
        try {
            return  $stmt->execute([$product->getId()]);
        } finally {
            $this->pdo->query('UNLOCK TABLES');
        }
    }
}
