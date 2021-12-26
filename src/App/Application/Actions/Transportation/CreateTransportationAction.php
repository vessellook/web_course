<?php
declare(strict_types=1);


namespace App\Application\Actions\Transportation;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Transportation\Transportation;
use Assert\Assert;
use Assert\Assertion;
use Assert\AssertionFailedException;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class CreateTransportationAction extends TransportationAction
{

    protected function action(): Response
    {
        $orderId = (int)$this->resolveArg('orderId');
        $params = $this->request->getParsedBody();
        try {
            TransportationAction::assertParams($params);
            $transportation = TransportationAction::convertParamsToTransportation(
                $params,
                orderId: $orderId
            );
            $transportation = $this->transportationRepository->createTransportation($transportation);
            $this->logger->info('Transportation created successfully');
            return $this->respondWithData($transportation);
        } catch (AssertionFailedException|Exception $e) {
            $this->logger->info($e->getMessage());
            $error = new ActionError($e::class, $e->getMessage());
            return $this->respond(new ActionPayload(statusCode: 400, error: $error));
        }
    }
}
