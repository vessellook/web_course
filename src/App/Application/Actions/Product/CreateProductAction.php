<?php
declare(strict_types=1);

namespace App\Application\Actions\Product;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Product\Product;
use App\Domain\Product\ProductCreationFailureException;
use App\Domain\Product\ProductNotFoundException;
use Assert\Assert;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class CreateProductAction extends ProductAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        if (!$body) {
            throw new HttpBadRequestException($this->request);
        }

        try {
            Assert::that($body)->isArray()
                ->keyExists('uid')
                ->keyExists('name')
                ->keyExists('price')
                ->keyExists('count');
//            Assertion::string($body['uid']);
//            Assertion::string($body['name']);
//            Assert::that($body['price'])->integer()->min(0);
//            Assert::that($body['count'])->integer()->min(0);
        } catch (AssertionFailedException $exception) {
            throw new HttpBadRequestException($this->request, $exception->getMessage());
        }

        $this->logger->info("Product was created.");

        $product = new Product(
            null,
            $body['uid'],
            $body['name'],
            $body['price'],
            $body['count']
        );
        try {
            $product = $this->productRepository->createProduct($product);
            return $this->respondWithData($product);
        } catch (ProductCreationFailureException $exception) {
            throw new HttpBadRequestException($this->request, $exception->getMessage());
        }
    }
}
