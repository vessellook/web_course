<?php

namespace App\Application\Middleware;

use App\Application\JwtGenerator\JwtGenerator;
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

    public function __construct(private LoggerInterface $logger, private JwtGenerator $jwtGenerator)
    {
    }

    /**
     * @inheritDoc
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $sessionTokenName = 'SESSION_TOKEN';
        if ($request->hasHeader($sessionTokenName)) {
            $this->logger->info('No SESSION_TOKEN received', ['line' => __LINE__, 'file' => __FILE__]);
            return new \Slim\Psr7\Response(status: 401);
        }
        $sessionToken = $request->getHeader($sessionTokenName)[0];
        try {
            $token = $this->jwtGenerator->parseToken($sessionToken);
            $this->jwtGenerator->validateToken($token);
            $userId = $token->headers()->get('userId');
            $role = $token->headers()->get('role', 'customer');
            $host = $request->getUri()->getHost();
            $newToken = $this->jwtGenerator->generateToken($userId, $host, $role);
            $response = $handler->handle($request
                ->withAttribute('userId', $userId)
                ->withAttribute('role', $role));
            $response->withHeader($sessionTokenName, $newToken->toString());
            return $response;
        } catch (CannotDecodeContent|InvalidTokenStructure|UnsupportedHeaderFound|RequiredConstraintsViolated
        $exception) {
            $this->logger->info($exception->getMessage(), ['line' => __LINE__, 'file' => __FILE__]);
            return new \Slim\Psr7\Response(status: 401);
        }
    }
}
