<?php
declare(strict_types=1);


namespace App\Application\Actions\Transportation;

use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class DeleteTransportationAction extends TransportationAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $transportationId = (int)$this->resolveArg('transportationId');
        if ($this->transportationRepository->deleteTransportation($transportationId)) {
            return $this->response;
        }
        return $this->response->withStatus(404);
    }
}
