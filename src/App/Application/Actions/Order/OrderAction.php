<?php
declare(strict_types=1);


namespace App\Application\Actions\Order;

use App\Application\Actions\Action;
use App\Domain\Order\OrderRepository;
use Psr\Log\LoggerInterface;

abstract class OrderAction extends Action
{

    public function __construct(
        LoggerInterface $logger,
        protected OrderRepository $orderRepository
    ) {
        parent::__construct($logger);
    }
}
