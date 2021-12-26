<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Domain\User\UserRepository;
use Assert\Assert;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Psr\Log\LoggerInterface;

abstract class UserAction extends Action
{
    protected UserRepository $userRepository;

    /**
     * @throws AssertionFailedException
     */
    public static function assertParams($params) {
        Assert::that($params)->isArray()
            ->keyExists('role')
            ->keyExists('login')
            ->keyExists('password');
        Assertion::notBlank($params['login']);
        Assertion::notBlank($params['password']);
        Assertion::inArray($params['role'], ['operator', 'director']);
    }


    public function __construct(
        LoggerInterface $logger,
        UserRepository $userRepository
    ) {
        parent::__construct($logger);
        $this->userRepository = $userRepository;
    }
}
