<?php

namespace App\Application\Actions\Product;

use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Product\ProductRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

abstract class ProductAction extends Action
{

    public function __construct(
        LoggerInterface             $logger,
        protected ProductRepository $productRepository
    ) {
        parent::__construct($logger);
    }
}
