<?php
declare(strict_types=1);

use App\Domain\Customer\CustomerRepository;
use App\Domain\Order\OrderRepository;
use App\Domain\Product\ProductRepository;
use App\Domain\Transportation\TransportationRepository;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Customer\PdoCustomerRepository;
use App\Infrastructure\Persistence\Order\PdoOrderRepository;
use App\Infrastructure\Persistence\Product\PdoProductRepository;
use App\Infrastructure\Persistence\Transportation\PdoTransportationRepository;
use App\Infrastructure\Persistence\User\PdoUserRepository;
use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        UserRepository::class => \DI\autowire(PdoUserRepository::class),
        ProductRepository::class => \DI\autowire(PdoProductRepository::class),
        OrderRepository::class => \DI\autowire(PdoOrderRepository::class),
        CustomerRepository::class => \DI\autowire(PdoCustomerRepository::class),
        TransportationRepository::class => \DI\autowire(PdoTransportationRepository::class),
    ]);
};
