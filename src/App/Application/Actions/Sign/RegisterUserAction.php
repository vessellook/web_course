<?php
declare(strict_types=1);

namespace App\Application\Actions\Sign;

use App\Application\Actions\Action;
use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Product\ProductRepository;
use App\Domain\User\User;
use App\Domain\User\UserRegistrationFailureException;
use App\Domain\User\UserRepository;
use Assert\Assert;
use Assert\Assertion;
use Assert\AssertionFailedException;
use PDO;
use phpDocumentor\Reflection\Types\This;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

class SignUpAction extends Action
{

    public function __construct(
        LoggerInterface        $logger,
        private UserRepository $userRepository
    ) {
        parent::__construct($logger);
    }

    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        try {
            Assert::that($body)->isArray()
                ->keyExists('login')
                ->keyExists('password')
                ->keyExists('email')
                ->keyExists('name');
            Assertion::notBlank($body['login']);
            Assertion::email($body['email']);
            Assertion::notBlank($body['password']);
            if (isset($body['phoneNumber'])) {
                Assert::that($body['phoneNumber'])->string();
            }
            $user = new User(
                id: null,
                role: 'customer',
                login: $body['login'],
                email: $body['email'],
                phoneNumber: $body['phoneNumber'] ?? null,
                passwordHash: $body['password'], // TODO: convert password to hash
                name: $body['name']
            );
            $this->userRepository->registerNewUser($user);
            $this->logger->info('user with login "' . $user->getLogin() . '" is registered');
            return $this->respondWithData($user);
        } catch (AssertionFailedException|UserRegistrationFailureException $exception) {
            $this->logger->info($exception->getMessage(), ['file' => __FILE__, 'line' => __LINE__]);
            $error = new ActionError($exception::class, $exception->getMessage());
            return $this->respond(new ActionPayload(statusCode: 400, error: $error));
        }
    }
}
