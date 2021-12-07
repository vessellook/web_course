<?php
declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {

            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError' => false,
                'logErrorDetails' => false,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../var/logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                'pathToDatabaseCreationScript' => dirname(__DIR__) . '/etc/recreate-database.sql',
                'mysql' => [
                    'password' => $_ENV['MYSQL_PASSWORD'] ?? 'wrong_password',
                    'user' => $_ENV['MYSQL_USER'] ?? 'wrong_user',
                    'database' => $_ENV['MYSQL_DATABASE'] ?? 'wrong_db',
                    'host' => $_ENV['MYSQL_HOST'] ?? 'wrong_host'
                ],
                'pathToPrivateKey' => dirname(__DIR__) . '/etc/jwtRS256.key',
                'pathToPublicKey' => dirname(__DIR__) . '/etc/jwtRS256.key.pub',
            ]);
        }
    ]);
};
