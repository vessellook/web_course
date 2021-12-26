<?php
declare(strict_types=1);


namespace App\Domain\Transportation;

use App\Domain\DomainException\DomainRecordCreationFailureException;
use App\Domain\DomainException\DomainRecordNotFoundException;

interface TransportationRepository
{
    /**
     * @return Transportation[]
     */
    public function findAll(): array;

    /**
     * @return Transportation[]
     */
    public function findAllOfOrder(int $orderId): array;

    /**
     * @param int $id
     * @return Transportation
     * @throws DomainRecordNotFoundException
     */
    public function findTransportationOfId(int $id): Transportation;

    /**
     * @param Transportation $transportation
     * @return Transportation
     * @throws DomainRecordCreationFailureException
     */
    public function createTransportation(Transportation $transportation): Transportation;

    public function updateTransportation(Transportation $old, Transportation $new): Transportation;

    public function deleteTransportation(int $transportationId): bool;
}
