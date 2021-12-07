<?php
declare(strict_types=1);

namespace App\Domain\Product;

interface ProductRepository
{
    /**
     * @return Product[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return Product
     * @throws ProductNotFoundException
     */
    public function findProductOfId(int $id): Product;

    /**
     * @param string $uid
     * @return Product
     * @throws ProductNotFoundException
     */
    public function findProductOfUid(string $uid): Product;

    /**
     * @param Product $product
     * @return Product
     * @throws ProductAlreadyExistsException
     */
    public function createProduct(Product $product): Product;
}
