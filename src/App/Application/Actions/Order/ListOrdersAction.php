<?php
declare(strict_types=1);


namespace App\Application\Actions\Order;

use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class ListOrdersAction extends OrderAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        // TODO: Implement action() method.
    }
}
