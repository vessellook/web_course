<?php
declare(strict_types=1);


namespace App\Application\Actions\Order;

use App\Domain\DomainException\DomainRecordCreationFailureException;
use Assert\AssertionFailedException;
use Exception;
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
            $this->logger->debug($customerId);
            $order = OrderAction::convertParamsToOrder(
                $params,
                customerId: $customerId ? intval($customerId) : null
            );
            $order = $this->orderRepository->createOrder($order);
            return $this->respondWithData($order, statusCode: 201);
        } catch (AssertionFailedException|DomainRecordCreationFailureException|Exception $exception) {
            $this->logger->error($exception->getMessage());
            throw new HttpBadRequestException($this->request, $exception->getMessage());
        }
    }
}
