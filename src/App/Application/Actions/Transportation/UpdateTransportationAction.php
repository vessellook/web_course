<?php
declare(strict_types=1);


namespace App\Application\Actions\Transportation;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Domain\Transportation\Transportation;
use Assert\Assert;
use Assert\Assertion;
use Assert\AssertionFailedException;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateTransportationAction extends TransportationAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $transportationId = (int)$this->resolveArg('transportationId');
        $params = $this->request->getParsedBody();
        try {
            Assert::that($params)->isArray()
                ->keyExists('old')
                ->keyExists('new');
            TransportationAction::assertParams($params['old']);
            TransportationAction::assertParams($params['new']);
            $oldTransportation = TransportationAction::convertParamsToTransportation(
                $params['old'],
                orderId: $params['old']['orderId'],
                id: $transportationId
            );
            $newTransportation = TransportationAction::convertParamsToTransportation(
                $params['new'],
                orderId: $params['new']['orderId'],
                id: $transportationId
            );

            $result = $this->transportationRepository->updateTransportation($oldTransportation, $newTransportation);
            if ($result->areSameAttributes($newTransportation)) {
                return $this->respondWithData($result);
            } else {
                return $this->respondWithData($result, statusCode: 409);
            }
        } catch (AssertionFailedException|Exception $e) {
            $this->logger->info($e->getMessage());
            $error = new ActionError($e::class, $e->getMessage());
            return $this->respond(new ActionPayload(statusCode: 400, error: $error));
        }
    }
}
