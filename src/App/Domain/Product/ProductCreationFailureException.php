<?php

namespace App\Domain\Product;

use App\Domain\DomainException\DomainRecordCreationFailureException;

class ProductCreationFailureException extends DomainRecordCreationFailureException
{
    public $message = "The product can't be created.";
}