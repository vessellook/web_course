<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Product;

use \Exception;
use App\Domain\Product\Product;
use App\Domain\Product\ProductAlreadyExistsException;
use App\Domain\Product\ProductNotFoundException;
use App\Domain\Product\ProductRepository;
use PDO;
use Psr\Log\LoggerInterface;

class PdoProductRepository implements ProductRepository
{

    private function convertRowToProduct(array $row): Product
    {
        return new Product(
            $row['id'],
            $row['uid'],
            $row['name'],
            $row['price'],
            $row['count']
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
        return array_map([$this, 'convertRowToProduct'], $stmt->fetchAll());
    }

    /**
     * @inheritDoc
     */
    public function findProductOfId(int $id): Product
    {
        $stmt = $this->pdo->query("SELECT * FROM product WHERE id = $id");
        if (!$stmt) {
            $this->logger->error('error in statement', ['line' => __LINE__, 'file' => __FILE__]);
            throw new ProductNotFoundException();
        }
        $row = $stmt->fetch();
        return $this->convertRowToProduct($row);
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
        return $this->convertRowToProduct($row);
    }

    public function createProduct(Product $product): Product
    {
        $stmt = 'INSERT INTO product (uid, name, price, count) VALUES (:uid, :name, :price, :count)';
        $stmt = $this->pdo->prepare($stmt);
        $stmt->bindValue('uid', $product->getUid());
        $stmt->bindValue('name', $product->getName());
        $stmt->bindValue('price', $product->getPrice());
        $stmt->bindValue('count', $product->getCount());
        if (!$stmt->execute()) {
            throw new ProductAlreadyExistsException();
        }
        $id = intval($this->pdo->lastInsertId());
        $stmt = $this->pdo->query("SELECT * FROM product WHERE id = $id");
        $row = $stmt->fetch();
        return $this->convertRowToProduct($row);
    }
}
