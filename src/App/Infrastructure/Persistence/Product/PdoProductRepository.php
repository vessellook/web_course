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
        $stmt = $this->pdo->query('SELECT * FROM product');
        if (!$stmt) {
            $this->logger->error('error in statement', ['line' => __LINE__, 'file' => __FILE__]);
            return [];
        }
        return array_map('self::convertRowToProduct', $stmt->fetchAll());
    }

    /**
     * @throws ProductNotFoundException
     */
    private function findProductById(int $id, $forUpdate = false): Product
    {
        $stmt = $this->pdo->query("SELECT * FROM product WHERE id = $id" . ($forUpdate ? ' FOR UPDATE' : ''));
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
        if (!$stmt->execute([$uid])) {
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
        if (!$stmt->execute()) {
            throw new ProductCreationFailureException();
        }
        $id = intval($this->pdo->lastInsertId());
        $stmt = $this->pdo->query("SELECT * FROM product WHERE id = $id");
        $row = $stmt->fetch();
        return PdoProductRepository::convertRowToProduct($row);
    }

    public function updateProduct(Product $old, Product $new): Product
    {
        $this->pdo->beginTransaction();
        try {
            $realOld = $this->findProductById($old->getId(), forUpdate: true);
            if (!$realOld->areSameAttributes($old)) {
                $this->pdo->rollBack();
                return $realOld;
            }
            $stmt = $this->pdo->query('UPDATE product SET uid = ?, name = ?, price = ? WHERE id = ?');
            $stmt->bindValue(1, $new->getUid());
            $stmt->bindValue(2, $new->getName());
            $stmt->bindValue(3, $new->getPrice());
            $stmt->bindValue(3, $old->getId());
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

    public function deleteProduct(Product $product): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM product WHERE id = ?');
        return $stmt->execute([$product->getId()]);
    }
}
