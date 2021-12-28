<?php
declare(strict_types=1);


namespace App\Application\Actions\Order;

use App\Application\Actions\Transportation\TransportationAction;
use App\Domain\DomainException\DomainRecordCreationFailureException;
use App\Domain\Order\OrderRepository;
use App\Domain\Transportation\TransportationRepository;
use Assert\AssertionFailedException;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

class CreateOrderAction extends OrderAction
{
    public function __construct(
        LoggerInterface                  $logger,
        OrderRepository                  $orderRepository
    ) {
        parent::__construct($logger, $orderRepository);
        $this->orderRepository = $orderRepository;
    }

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $customerId = $this->request->getAttribute('customerId');
        $params = $this->request->getParsedBody();
        try {
            OrderAction::assertOrder($params['order']);
            $this->logger->debug($customerId);
            $order = OrderAction::convertParamsToOrder(
                $params['order'],
                customerId: $customerId ? intval($customerId) : null
            );
            array_walk($params['transportations'], fn(&$t) => $t['orderId'] =$order->getId());
            array_walk($params['transportations'], fn($t) => TransportationAction::assertParams($t));
            $transportations = array_map(
                fn($t) => TransportationAction::convertParamsToTransportation($t, $order->getId()),
                $params['transportations']
            );
            $order = $this->orderRepository->createOrderWithTransportations($order, $transportations);
            return $this->respondWithData($order, statusCode: 201);
        } catch (AssertionFailedException|DomainRecordCreationFailureException|Exception $exception) {
            $this->logger->error($exception->getMessage());
            throw new HttpBadRequestException($this->request, $exception->getMessage());
        }
    }
}
