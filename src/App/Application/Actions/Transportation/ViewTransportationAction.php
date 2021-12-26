<?php
declare(strict_types=1);


namespace App\Application\Actions\Transportation;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class ViewTransportationAction extends TransportationAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $transportationId = (int)$this->resolveArg('transportationId');
        try {
            $transportation = $this->transportationRepository->findTransportationOfId($transportationId);
            return $this->respondWithData($transportation);
        } catch (DomainRecordNotFoundException $e) {
            $this->logger->info($e->getMessage());
            $error = new ActionError($e::class, $e->getMessage());
            return $this->respond(new ActionPayload(statusCode: 400, error: $error));
        }
    }
}
