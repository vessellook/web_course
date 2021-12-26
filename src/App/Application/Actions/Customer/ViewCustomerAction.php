<?php
declare(strict_types=1);


namespace App\Application\Actions\Customer;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Domain\Customer\CustomerNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;

class ViewCustomerAction extends CustomerAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $customerId = (int)$this->resolveArg('customerId');
        try {
            $customer = $this->customerRepository->findCustomerOfId($customerId);
            return $this->respondWithData($customer);
        } catch (CustomerNotFoundException $e) {
            $this->logger->info($e->getMessage());
            $error = new ActionError($e::class, $e->getMessage());
            return $this->respond(new ActionPayload(statusCode: 400, error: $error));
        }
    }
}
