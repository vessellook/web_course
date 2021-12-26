<?php
declare(strict_types=1);


namespace App\Application\Actions\Order;

use App\Domain\DomainException\DomainRecordCreationFailureException;
use Assert\AssertionFailedException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class CreateOrderAction extends OrderAction
{
    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $customerId = $this->request->getAttribute('customerId');
        $params = $this->request->getParsedBody();
        try {
            OrderAction::assertOrder($params);
            $order = OrderAction::convertParamsToOrder(
                $params,
                customerId: $customerId
            );
            $order = $this->orderRepository->createOrder($order);
            return $this->respondWithData($order);
        } catch (AssertionFailedException|DomainRecordCreationFailureException $exception) {
            throw new HttpBadRequestException($this->request, $exception->getMessage());
        }
    }
}
