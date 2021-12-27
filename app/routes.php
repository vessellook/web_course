<?php
declare(strict_types=1);

use App\Application\Actions\Customer\CreateCustomerAction;
use App\Application\Actions\Customer\DeleteCustomerAction;
use App\Application\Actions\Customer\ListCustomersAction;
use App\Application\Actions\Customer\UpdateCustomerAction;
use App\Application\Actions\Customer\ViewCustomerAction;
use App\Application\Actions\DummyAction;
use App\Application\Actions\Order\CreateOrderAction;
use App\Application\Actions\Order\DeleteOrderAction;
use App\Application\Actions\Order\ListOrdersAction;
use App\Application\Actions\Order\UpdateOrderAction;
use App\Application\Actions\Order\ViewOrderAction;
use App\Application\Actions\Product\ListProductsAction;
use App\Application\Actions\Setup\CreateDatabaseAction;
use App\Application\Actions\Setup\FillDatabaseAction;
use App\Application\Actions\Sign\GenerateTokenAction;
use App\Application\Actions\Transportation\CreateTransportationAction;
use App\Application\Actions\Transportation\DeleteTransportationAction;
use App\Application\Actions\Transportation\ListTransportationsAction;
use App\Application\Actions\Transportation\UpdateTransportationAction;
use App\Application\Actions\Transportation\ViewTransportationAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\RegisterUserAction;
use App\Application\Actions\User\UpdateUserPasswordAction;
use App\Application\Actions\User\ViewUserAction;
use App\Application\Middleware\JwtAuthenticationMiddleware;
use App\Application\Middleware\RoleBasedAuthorizationMiddleware;
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
            $group->patch('/{id}', UpdateUserPasswordAction::class);
        })->addMiddleware(new RoleBasedAuthorizationMiddleware(['director']))->add(JwtAuthenticationMiddleware::class);

        $api->get('/token/update', DummyAction::class)
            ->add(JwtAuthenticationMiddleware::class);
        $api->get('/token[?.*]', GenerateTokenAction::class);
        $api->group('/profile', function (Group $group) {
            $group->get('', ViewUserAction::class);
            $group->patch('', UpdateUserPasswordAction::class);
        })->add(JwtAuthenticationMiddleware::class);

        $api->group('/products', function (Group $group) {
            $group->get('', ListProductsAction::class);
//            $group->post('', CreateProductAction::class);
        })->add(JwtAuthenticationMiddleware::class);

        $api->group('/customers', function (Group $group) {
            $group->get('', ListCustomersAction::class);
            $group->post('', CreateCustomerAction::class);
            $group->get('/{customerId}', ViewCustomerAction::class);
            $group->delete('/{customerId}', DeleteCustomerAction::class);
            $group->put('/{customerId}', UpdateCustomerAction::class);
            $group->get('/{customerId}/orders', ListOrdersAction::class);
            $group->post('/{customerId}/orders', CreateOrderAction::class);
        })->add(JwtAuthenticationMiddleware::class);

        $api->group('/orders', function (Group $group) {
            $group->get('', ListOrdersAction::class);
            $group->get('/{orderId}', ViewOrderAction::class);
            $group->put('/{orderId}', UpdateOrderAction::class);
            $group->delete('/{orderId}', DeleteOrderAction::class);
            $group->get('/{orderId}/transportations', ListTransportationsAction::class);
            $group->post('/{orderId}/transportations', CreateTransportationAction::class);
        })->add(JwtAuthenticationMiddleware::class);

        $api->group('/transportations', function (Group $group) {
            $group->get('', ListTransportationsAction::class);
            $group->get('/{transportationId}', ViewTransportationAction::class);
            $group->put('/{transportationId}', UpdateTransportationAction::class);
            $group->delete('/{transportationId}', DeleteTransportationAction::class);
        })->add(JwtAuthenticationMiddleware::class);
    });

    $app->group('/setup', function (Group $group) {
        $group->any('/create-empty-database', CreateDatabaseAction::class);
        $group->any('/fill-database', FillDatabaseAction::class);
    });

    $app->get('/info', function (Request $request, Response $response) {
        ob_start();
        phpinfo();
        $response->getBody()->write(ob_get_clean());
        return $response;
    });
};
