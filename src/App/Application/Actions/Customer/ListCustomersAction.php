<?php
declare(strict_types=1);


namespace App\Application\Actions\Customer;

use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class ListCustomersAction extends CustomerAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $customers = $this->customerRepository->findAll();
        return $this->respondWithData($customers);
    }
}
