<?php
declare(strict_types=1);


namespace App\Application\Actions\Order;

use App\Application\Actions\Action;
use App\Domain\Order\Order;
use App\Domain\Order\OrderRepository;
use Assert\Assert;
use Assert\Assertion;
use Assert\AssertionFailedException;
use DateTimeImmutable;
use DateTimeInterface;
use Psr\Log\LoggerInterface;

abstract class OrderAction extends Action
{

    /**
     * @throws AssertionFailedException
     */
    public static function assertOrder($params)
    {
        Assert:: that($params)->isArray()
            ->keyExists('customerId')
            ->keyExists('productId')
            ->keyExists('address');
        Assertion::integer($params['customerId']);
        Assertion::integer($params['productId']);
        Assertion::notBlank($params['address']);
        Assertion::nullOrDate($params['date'], DateTimeInterface::ATOM);
        Assertion::nullOrString($params['agreementCode']);
        Assertion::nullOrString($params['agreementUrl']);
    }

    public static function convertParamsToOrder($params, $customerId, $id = null): Order
    {
        $date = null;
        if (isset($params['date'])) {
            $date = DateTimeImmutable::createFromFormat($params['date'], DateTimeInterface::ATOM);
        }
        return new Order(
            id: $id,
            customerId: $customerId,
            productId: $params['productId'],
            address: $params['address'],
            date: $date,
            agreementCode: $params['agreementCode'] ?? null,
            agreementUrl: $params['agreementUrl'] ?? null,
        );
    }

    public function __construct(
        LoggerInterface           $logger,
        protected OrderRepository $orderRepository
    )
    {
        parent::__construct($logger);
    }
}
