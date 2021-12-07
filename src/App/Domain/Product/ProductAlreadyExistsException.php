<?php

namespace App\Domain\Product;

use App\Domain\DomainException\DomainRecordAlreadyExistsException;

class ProductAlreadyExistsException extends DomainRecordAlreadyExistsException
{
    public $message = 'The product you try to create exists.';
}