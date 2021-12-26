<?php
declare(strict_types=1);


namespace App\Application\Actions\Customer;

use App\Application\Actions\Action;
use App\Domain\Customer\CustomerRepository;
use Psr\Log\LoggerInterface;

abstract class CustomerAction extends Action
{
    public function __construct(LoggerInterface $logger, protected CustomerRepository $customerRepository)
    {
        parent::__construct($logger);
    }
}
