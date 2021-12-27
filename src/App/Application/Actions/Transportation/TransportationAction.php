<?php
declare(strict_types=1);


namespace App\Application\Actions\Transportation;

use App\Application\Actions\Action;
use App\Domain\Transportation\Transportation;
use App\Domain\Transportation\TransportationRepository;
use Assert\Assert;
use Assert\Assertion;
use Assert\AssertionFailedException;
use DateTimeInterface;
use Psr\Log\LoggerInterface;
use DateTimeImmutable;

abstract class TransportationAction extends Action
{
    /**
     * @throws AssertionFailedException
     */
    public static function assertParams($params)
    {
        Assert::that($params)->isArray()
            ->keyExists('plannedDate')
            ->keyExists('number')
            ->keyExists('status');
        Assert::that($params['plannedDate'])->notBlank()->date('Y-m-d\TH:i:s.v\Z');
        Assertion::integer($params['number']);
        Assertion::nullOrDate($params['realDate'], 'Y-m-d\TH:i:s.v\Z');
        Assertion::inArray($params['status'], ['planned', 'finished']);
    }

    /**
     * @throws AssertionFailedException
     */
    public static function convertParamsToTransportation($params, $orderId, $id = null): Transportation
    {
        $realDate = null;
        if (isset($params['realDate'])) {
            $realDate = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.v\Z', $params['realDate']);
        }
        return new Transportation(
            id: $id,
            orderId: $orderId,
            plannedDate: DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.v\Z', $params['plannedDate']),
            realDate: $realDate,
            number: $params['number'],
            status: $params['status']
        );
    }

    public function __construct(
        LoggerInterface                    $logger,
        protected TransportationRepository $transportationRepository
    )
    {
        parent::__construct($logger);
    }
}
