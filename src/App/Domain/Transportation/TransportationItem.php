<?php
declare(strict_types=1);


namespace App\Domain\Transportation;


use JsonSerializable;

class TransportationItem implements JsonSerializable
{

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        // TODO: Implement jsonSerialize() method.
    }
}
