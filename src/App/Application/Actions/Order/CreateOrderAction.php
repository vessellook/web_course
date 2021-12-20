<?php
declare(strict_types=1);


namespace App\Application\Actions\Order;

use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Order\Order;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class CreateOrderAction extends OrderAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $this->logger->info('request to create order');
        return $this->respondWithData(new Order(id: 0, address: 0));
    }
}
