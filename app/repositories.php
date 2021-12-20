<?php
declare(strict_types=1);

use App\Domain\Order\OrderRepository;
use App\Domain\Product\ProductRepository;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Order\PdoOrderRepository;
use App\Infrastructure\Persistence\Product\PdoProductRepository;
use App\Infrastructure\Persistence\User\PdoUserRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    // Here we map our UserRepository interface to its in memory implementation
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(PdoUserRepository::class),
        ProductRepository::class => \DI\autowire(PdoProductRepository::class),
        OrderRepository::class => \DI\autowire(PdoOrderRepository::class)
    ]);
};
