<?php
declare(strict_types=1);


namespace App\Application\Actions\Order;

use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class DeleteOrderAction extends OrderAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $orderId = (int)$this->resolveArg('orderId');
        if ($this->orderRepository->deleteOrder($orderId)) {
            return $this->response;
        }
        return $this->response->withStatus(404);
    }
}
