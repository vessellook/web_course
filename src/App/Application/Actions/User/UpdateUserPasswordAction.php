<?php
declare(strict_types=1);


namespace App\Application\Actions\User;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\User\User;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;

class UpdateUserPasswordAction extends UserAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $userId = $this->args['userId'] ?? $this->request->getAttribute('userId');
        try {
            Assertion::notNull($userId);
            $userId = intval($userId);
            Assertion::keyExists($body, 'password');
            Assertion::notBlank($body['password']);
            $oldUser = $this->userRepository->findUserOfId($userId);
            $newUser = new User(
                id: $oldUser->getId(),
                role: $oldUser->getRole(),
                login: $oldUser->getLogin(),
                password: $body['password']
            );
            $result = $this->userRepository->updateUser($oldUser, $newUser);
            if ($result->areSameAttributes($newUser)) {
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
