<?php

namespace App\Application\Actions\Setup;

use App\Application\Actions\ActionPayload;
use App\Application\SqlScripts\CreateDatabaseScript;
use App\Domain\DomainException\DomainRecordNotFoundException;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

class CreateDatabaseAction extends DatabaseAction
{

    private string $query;

    public function __construct(LoggerInterface $logger, PDO $pdo, CreateDatabaseScript $script)
    {
        parent::__construct($logger, $pdo);
        $this->query = $script->getQuery();
    }

  /**
   * @inheritDoc
   */
    protected function action(): Response
    {
        if ($this->pdo->query($this->query)) {
            return $this->respond(new ActionPayload(statusCode: 201));
        }
        return $this->respond(new ActionPayload(statusCode: 500));
    }
}
