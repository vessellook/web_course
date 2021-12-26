<?php
declare(strict_types=1);


namespace App\Application\Actions\Customer;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Domain\Customer\Customer;
use Assert\Assert;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateCustomerAction extends CustomerAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $customerId = (int)$this->resolveArg('customerId');
        $params = $this->request->getParsedBody();
        try {
            Assert::that($params)->keyExists('old')->keyExists('new');
            Assert::that($params['old'])->isArray()->keyExists('name');
            Assertion::notBlank($params['old']['name']);
            Assert::that($params['new'])->isArray()->keyExists('name');
            Assertion::notBlank($params['new']['name']);

            $oldCustomer = new Customer(
                id: $customerId,
                name: $params['old']['name'],
                address: $params['old']['address'] ?? null,
                phoneNumber: $params['old']['phoneNumber'] ?? null
            );

            $newCustomer = new Customer(
                id: $customerId,
                name: $params['new']['name'],
                address: $params['new']['address'] ?? null,
                phoneNumber: $params['new']['phoneNumber'] ?? null
            );

            $result = $this->customerRepository->updateCustomer($oldCustomer, $newCustomer);
            if ($result->areSameAttributes($newCustomer)) {
                return $this->respondWithData($result);
            } else {
                return $this->respondWithData($result, statusCode: 409);
            }
        } catch (AssertionFailedException $e) {
            $this->logger->info($e->getMessage());
            $error = new ActionError($e::class, $e->getMessage());
            return $this->respond(new ActionPayload(statusCode: 400, error: $error));
        }
    }
}
