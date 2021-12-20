<?php
declare(strict_types=1);


namespace App\Domain\User;

use App\Domain\DomainException\DomainRecordCreationFailureException;

class UserRegistrationFailureException extends DomainRecordCreationFailureException
{
    public $message = "The user can't be registered.";
}
