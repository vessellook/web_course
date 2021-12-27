<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Domain\User\User;
use App\Domain\User\UserRegistrationFailureException;
use Assert\AssertionFailedException;
use Psr\Http\Message\ResponseInterface as Response;

class RegisterUserAction extends UserAction
{

    protected function action(): Response
    {
        $params = $this->request->getParsedBody();
        try {
            $this->assertParams($params);
            $this->logger->info(0);
            $user = new User(
                id: null,
                role: $params['role'],
                login: $params['login'],
                password: $params['password'], // TODO: convert password to hash
            );
            $this->logger->info(1);
            $user = $this->userRepository->registerNewUser($user);
            $this->logger->info(2);
            $this->logger->info('user with login "' . $user->getLogin() . '" is registered');
            return $this->respondWithData($user);
        } catch (AssertionFailedException|UserRegistrationFailureException $exception) {
            $this->logger->info($exception->getMessage(), ['file' => __FILE__, 'line' => __LINE__]);
            $error = new ActionError($exception::class, $exception->getMessage());
            return $this->respond(new ActionPayload(statusCode: 400, error: $error));
        }
    }
}
