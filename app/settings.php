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
            $inactivityTimeout = intval($_ENV['INACTIVITY_TIMEOUT']);
            if ($inactivityTimeout) {
                $inactivityTimeout = new DateInterval("PT${inactivityTimeout}S");
            }
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
                    'password' => $_ENV['MYSQL_PASSWORD'] ?? throw new Exception('specify password for database'),
                    'user' => $_ENV['MYSQL_USER'] ?? throw new Exception('specify user for database'),
                    'database' => $_ENV['MYSQL_DATABASE'] ?? throw new Exception('specify name for database'),
                    'host' => $_ENV['MYSQL_HOST'] ?? throw new Exception('specify host for database')
                ],
                'pathToPrivateKey' => dirname(__DIR__) . '/etc/jwtRS256.key',
                'pathToPublicKey' => dirname(__DIR__) . '/etc/jwtRS256.key.pub',
                'inactivityTimeout' => $inactivityTimeout ?? throw new Exception('specify inactivity timeout'),
            ]);
        }
    ]);
};
