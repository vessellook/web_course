<?php
declare(strict_types=1);

namespace App\Domain\Customer;

use App\Domain\DomainException\DomainRecordNotFoundException;

class CustomerCanNotBeCreated extends DomainRecordNotFoundException
{
    public $message = "The customer can't be created.";
}
