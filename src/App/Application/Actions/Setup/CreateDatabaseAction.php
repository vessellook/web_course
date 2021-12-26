<?php

namespace App\Application\Actions\Setup;

use App\Application\Actions\Action;
use App\Application\Actions\ActionPayload;
use App\Application\SqlScripts\CreateDatabaseScript;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class CreateDatabaseAction extends Action
{
    protected string $query;

    public function __construct(LoggerInterface $logger, private CreateDatabaseScript $script)
    {
        parent::__construct($logger);
    }

  /**
   * @inheritDoc
   */
    protected function action(): Response
    {
        if ($this->script->run()) {
            return $this->respond(new ActionPayload(statusCode: 201));
        }
        return $this->respond(new ActionPayload(statusCode: 500));
    }
}
