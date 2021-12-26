<?php
declare(strict_types=1);


namespace App\Application\Actions\Order;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use Assert\Assert;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateOrderAction extends OrderAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $orderId = (int)$this->resolveArg('orderId');
        $params = $this->request->getParsedBody();
        try {
            Assertion::notBlank($orderId);
            Assert::that($params)->isArray()->keyExists('old')->keyExists('new');
            OrderAction::assertOrder($params['old']);
            OrderAction::assertOrder($params['new']);

            $oldOrder = OrderAction::convertParamsToOrder(
                $params['old'],
                customerId: $params['old']['customerId'],
                id: $orderId
            );
            $newOrder = OrderAction::convertParamsToOrder(
                $params['new'],
                customerId: $params['new']['customerId'],
                id: $orderId
            );
            $result = $this->orderRepository->updateOrder($oldOrder, $newOrder);
            if ($result->areSameAttributes($newOrder)) {
                return $this->respondWithData($result);
            } else {
                return $this->respondWithData($result, statusCode: 409);
            }
        } catch (AssertionFailedException $e) {
            $this->logger->info($e->getMessage());
            $error = new ActionError($e::class, $e->getMessage());
            return $this->respond(new ActionPayload(statusCode: 400, error: $error));
        }
    }
}
