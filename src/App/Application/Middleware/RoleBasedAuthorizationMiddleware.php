<?php
declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class RoleBasedAuthorizationMiddleware implements MiddlewareInterface
{

    public function __construct(private array $allowedRoles)
    {
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $role = $request->getAttribute('role');
        if (!$role || !in_array($role, $this->allowedRoles)) {
            return new Response(status: 403);
        }
        return $handler->handle($request);
    }
}
