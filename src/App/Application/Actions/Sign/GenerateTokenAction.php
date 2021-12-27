<?php
declare(strict_types=1);

namespace App\Application\Actions\Sign;

use App\Application\Actions\Action;
use App\Application\JwtGenerator\JwtGenerator;
use App\Application\Middleware\JwtAuthenticationMiddleware;
use App\Domain\Product\ProductRepository;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;
use Assert\Assert;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Headers;

class GenerateTokenAction extends Action
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
        $body = $this->request->getQueryParams();
        try {
            Assert::that($body)->isArray()
                ->keyExists('login')
                ->keyExists('password');
            Assertion::notBlank($body['login']);
            $user = $this->userRepository->findUserOfLogin($body['login']);
            // TODO: use password_verify or wrapper around it
            if ($user->getPassword() !== $body['password']) {
                return new \Slim\Psr7\Response(status: 400);
            }
            $host = $this->request->getUri()->getHost();
            $token = $this->jwtGenerator->generateToken($user->getId(), $host, $user->getRole());
            return new \Slim\Psr7\Response(status: 200, headers: new Headers([
                JwtAuthenticationMiddleware::SESSION_TOKEN_NAME => $token->toString()
            ]));
        } catch (AssertionFailedException|UserNotFoundException $exception) {
            $this->logger->info($exception->getMessage(), ['file' => __FILE__, 'line' => __LINE__]);
            return new \Slim\Psr7\Response(status: 400);
        }
    }
}
