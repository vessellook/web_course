<?php
declare(strict_types=1);


namespace App\Application\Actions;

use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class DummyAction extends Action
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        return $this->respond(new ActionPayload(data: 'success'));
    }
}
