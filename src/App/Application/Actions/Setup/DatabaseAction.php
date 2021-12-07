<?php

namespace App\Application\Actions\Setup;

use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

abstract class DatabaseAction extends Action
{
    protected PDO $pdo;

    public function __construct(LoggerInterface $logger, PDO $pdo)
    {
        parent::__construct($logger);
        $this->pdo = $pdo;
    }
}