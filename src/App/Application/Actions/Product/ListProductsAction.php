<?php

namespace App\Application\Actions\Product;

use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class ListProductsAction extends ProductAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $products = $this->productRepository->findAll();

        $this->logger->info("Products list was viewed.");

        return $this->respondWithData($products);
    }
}