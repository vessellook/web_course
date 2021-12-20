<?php
declare(strict_types=1);

namespace App\Application\Actions\Sign;

use App\Application\Actions\Action;
use App\Application\JwtGenerator\JwtGenerator;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Product\ProductRepository;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;
use Assert\Assert;
use Assert\Assertion;
use Assert\AssertionFailedException;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

class GetTokenAction extends Action
{

    public function __construct(
        LoggerInterface             $logger,
        protected ProductRepository $productRepository,
        private UserRepository      $userRepository,
        private JwtGenerator $jwtGenerator
    ) {
        parent::__construct($logger);
    }

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        try {
            Assert::that($body)->isArray()
                ->keyExists('login')
                ->keyExists('password');
            Assert::that($body['login'])->string()->notBlank();
            $user = $this->userRepository->findUserOfLogin($body['login']);
            // TODO: use password_verify or wrapper around it
            if ($user->getPasswordHash() !== $body['password']) {
                return new \Slim\Psr7\Response(status: 400);
            }
            return new \Slim\Psr7\Response(status: 200);
        } catch (AssertionFailedException|UserNotFoundException) {
            return new \Slim\Psr7\Response(status: 400);
        }
    }
}
