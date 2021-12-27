<?php
declare(strict_types=1);


namespace App\Application\Actions\Order;

use App\Application\Actions\ActionPayload;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class ListOrdersAction extends OrderAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $customerId = $this->args['customerId'] ?? null;
        if (isset($customerId)) {
            $orders = $this->orderRepository->findAllOfCustomer(intval($customerId));
        } else {
            $orders = $this->orderRepository->findAll();
        }
        return $this->respondWithData($orders);
    }
}
