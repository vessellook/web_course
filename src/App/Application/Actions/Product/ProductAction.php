<?php

namespace App\Application\Actions\Product;

use App\Application\Actions\Action;
use App\Domain\Product\ProductRepository;
use Psr\Log\LoggerInterface;

abstract class ProductAction extends Action
{

    public function __construct(
        LoggerInterface             $logger,
        protected ProductRepository $productRepository
    ) {
        parent::__construct($logger);
    }
}
