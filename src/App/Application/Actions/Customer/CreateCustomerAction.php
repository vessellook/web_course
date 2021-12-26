<?php
declare(strict_types=1);


namespace App\Application\Actions\Customer;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Domain\Customer\Customer;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Assert\Assert;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class CreateCustomerAction extends CustomerAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $params = $this->request->getParsedBody();
        try {
            Assert::that($params)->isArray()->keyExists('name');
            Assertion::notBlank($params['name']);
            $customer = new Customer(
                id: null,
                name: $params['name'],
                address: $params['address'] ?? null,
                phoneNumber: $params['phoneNumber'] ?? null
            );
            $customer = $this->customerRepository->createCustomer($customer);
            return $this->respondWithData($customer);
        } catch (AssertionFailedException $e) {
            $this->logger->info($e->getMessage());
            $error = new ActionError($e::class, $e->getMessage());
            return $this->respond(new ActionPayload(statusCode: 400, error: $error));
        }
    }
}
