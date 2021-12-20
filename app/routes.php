<?php
declare(strict_types=1);

use App\Application\Actions\DummyAction;
use App\Application\Actions\Product\CreateProductAction;
use App\Application\Actions\Product\ListProductsAction;
use App\Application\Actions\Setup\FillDatabaseAction;
use App\Application\Actions\Sign\GenerateTokenAction;
use App\Application\Actions\Sign\RegisterUserAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Actions\Setup\CreateDatabaseAction;
use App\Application\Middleware\JwtAuthenticationMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->group('/api/v1', function (Group $api) {
        $api->group('/users', function (Group $group) {
            $group->get('', ListUsersAction::class);
            $group->post('', RegisterUserAction::class);
            $group->get('/{id}', ViewUserAction::class);
        });

        $api->get('/token#update', DummyAction::class)->add(JwtAuthenticationMiddleware::class);
        $api->get('/token[?.*]', GenerateTokenAction::class);

        $api->group('/products', function (Group $group) {
            $group->get('', ListProductsAction::class);
            $group->post('', CreateProductAction::class);
        });

        $api->group('/orders', function (Group $group) {
            $group->get('', ListProductsAction::class);
            $group->post('', CreateProductAction::class);
        })->add(JwtAuthenticationMiddleware::class);
    });

    $app->group('/setup', function (Group $group) {
        $group->post('/create-empty-database', CreateDatabaseAction::class);
        $group->post('/fill-database', FillDatabaseAction::class);
    });

    $app->get('/info', function (Request $request, Response $response) {
        ob_start();
        phpinfo();
        $response->getBody()->write(ob_get_clean());
        return $response;
    });
};
