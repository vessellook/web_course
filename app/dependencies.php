<?php
declare(strict_types=1);

use App\Application\JwtGenerator\JwtGenerator;
use App\Application\Settings\SettingsInterface;
use App\Application\SqlScripts\CreateDatabaseScript;
use DI\ContainerBuilder;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Lcobucci\Clock\FrozenClock;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        PDO::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $credentials = $settings->get('mysql');
            $dbname = $credentials['database'];
            $host = $credentials['host'];
            $user = $credentials['user'];
            $password = $credentials['password'];
            return new PDO("mysql:dbname=$dbname;host=$host", $user, $password);
        },
        CreateDatabaseScript::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $pathToScript = $settings->get('pathToDatabaseCreationScript');
            $pdo = $c->get(PDO::class);
            return new CreateDatabaseScript($pathToScript, $pdo);
        },
        Configuration::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            return Configuration::forAsymmetricSigner(
                new Signer\Rsa\Sha256(),
                InMemory::file($settings->get('pathToPrivateKey')),
                InMemory::file($settings->get('pathToPublicKey'))
            );
        },
        JwtGenerator::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);
            $configuration = $c->get(Configuration::class);
            return new JwtGenerator($configuration, $settings->get('inactivityTimeout'));
        }
    ]);
};
