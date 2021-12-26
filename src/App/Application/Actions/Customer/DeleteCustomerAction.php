<?php
declare(strict_types=1);


namespace App\Application\Actions\Customer;

use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class DeleteCustomerAction extends CustomerAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $customerId = (int)$this->resolveArg('customerId');
        if ($this->customerRepository->deleteCustomer($customerId)) {
            return $this->response;
        }
        return $this->response->withStatus(404);
    }
}
