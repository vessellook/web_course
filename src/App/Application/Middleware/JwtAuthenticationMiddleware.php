<?php

namespace App\Application\Middleware;

use App\Application\JwtGenerator\JwtGenerator;
use Assert\AssertionFailedException;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;

class JwtAuthenticationMiddleware implements Middleware
{
    public const SESSION_TOKEN_NAME = 'SESSION-TOKEN';

    public function __construct(private LoggerInterface $logger, private JwtGenerator $jwtGenerator)
    {
    }

    /**
     * @inheritDoc
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        if (!$request->hasHeader(self::SESSION_TOKEN_NAME)) {
            $this->logger->info('No SESSION-TOKEN received', ['line' => __LINE__, 'file' => __FILE__]);
            return new \Slim\Psr7\Response(status: 401);
        }
        $sessionToken = $request->getHeader(self::SESSION_TOKEN_NAME)[0];
        try {
            $token = $this->jwtGenerator->parseToken($sessionToken);
            $this->logger->info('token parsing succeeded');
            $this->jwtGenerator->assertToken($token);
            $this->logger->info('token assertion succeeded');
            $userId = $token->claims()->get('userId');
            $role = $token->claims()->get('role', 'customer');
            $host = $request->getUri()->getHost();
            $newToken = $this->jwtGenerator->generateToken($userId, $host, $role);
            return $handler->handle($request
                ->withAttribute('userId', $userId)
                ->withAttribute('role', $role))
                ->withHeader(self::SESSION_TOKEN_NAME, $newToken->toString());
        } catch (AssertionFailedException|CannotDecodeContent|InvalidTokenStructure|UnsupportedHeaderFound
        |RequiredConstraintsViolated $exception) {
            $this->logger->info($exception->getMessage(), ['line' => __LINE__, 'file' => __FILE__]);
            return new \Slim\Psr7\Response(status: 401);
        }
    }
}
