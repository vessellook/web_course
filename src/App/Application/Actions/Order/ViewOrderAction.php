<?php
declare(strict_types=1);


namespace App\Application\Actions\Order;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Domain\Order\OrderNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class ViewOrderAction extends OrderAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $orderId = (int)$this->resolveArg('orderId');
        try {
            $order = $this->orderRepository->findOrderOfId($orderId);
            return $this->respondWithData($order);
        } catch (OrderNotFoundException $e) {
            $this->logger->info($e->getMessage());
            $error = new ActionError($e::class, $e->getMessage());
            return $this->respond(new ActionPayload(statusCode: 400, error: $error));
        }
    }
}
