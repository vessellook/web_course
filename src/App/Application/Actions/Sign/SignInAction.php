<?php
declare(strict_types=1);

namespace App\Application\Actions\Sign;

use App\Application\Actions\Action;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Product\ProductRepository;
use Assert\Assert;
use Assert\AssertionFailedException;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;

class SignInAction extends Action
{

    public function __construct(LoggerInterface             $logger,
                                protected ProductRepository $productRepository,
                                private PDO                 $pdo)
    {
        parent::__construct($logger);
    }

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        try {
            Assert::that($body)->isArray()->keyExists('login')->keyExists('password');
            Assert::that($body['login'])->string()->email();
        } catch (AssertionFailedException) {
            return new \Slim\Psr7\Response(status: 400);
        }

    }
}
