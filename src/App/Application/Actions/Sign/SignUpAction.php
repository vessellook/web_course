<?php
declare(strict_types=1);

namespace App\Application\Actions\Sign;

use App\Application\Actions\Action;
use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Product\ProductRepository;
use Assert\Assert;
use Assert\AssertionFailedException;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

class SignUpAction extends Action
{

    public function __construct(LoggerInterface             $logger,
                                private PDO                 $pdo)
    {
        parent::__construct($logger);
    }

    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        try {
            Assert::that($body)->isArray()->keyExists('login')->keyExists('password');
            Assert::that($body['login'])->string()->email();
            return
        } catch (AssertionFailedException $exception) {
            $error = new ActionError($exception::class, $exception->getMessage());
            return $this->respond(new ActionPayload(statusCode: 400, error: $error));
        }
    }
}
