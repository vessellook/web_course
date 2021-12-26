<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Transportation;

use App\Domain\DomainException\DomainRecordCreationFailureException;
use App\Domain\DomainException\DomainRecordNotFoundException;
use App\Domain\Transportation\Transportation;
use App\Domain\Transportation\TransportationRepository;
use Assert\AssertionFailedException;
use DateTimeImmutable;
use Exception;
use PDO;

class PdoTransportationRepository implements TransportationRepository
{

    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @throws AssertionFailedException
     */
    private static function convertRowToTransportation($row): Transportation
    {
        $plannedDate = DateTimeImmutable::createFromFormat('Y-m-d', $row['planned_date']);

        $realDate = null;
        if (isset($row['real_date'])) {
            $realDate = DateTimeImmutable::createFromFormat('Y-m-d', $row['real_date']);
        }
        return new Transportation(
            id: $row['id'],
            orderId: $row['order_id'],
            plannedDate: $plannedDate ?? null,
            realDate: $realDate ?? null,
            number: $row['number'],
            status: $row['status']
        );
    }

    /**
     * @inheritDoc
     * @return Transportation[]
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM transportation');
        $rows = $stmt->fetchAll();
        if (!$rows) {
            return [];
        }
        return array_map('self::convertRowToTransportation', $rows);
    }

    /**
     * @inheritDoc
     * @return Transportation[]
     */
    public function findAllOfOrder(int $orderId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM transportation WHERE order_id = ?');
        $stmt->execute([$orderId]);
        $rows = $stmt->fetchAll();
        if (!$rows) {
            return [];
        }
        return array_map('self::convertRowToTransportation', $rows);
    }

    /**
     * @throws DomainRecordNotFoundException|AssertionFailedException
     */
    private function findTransportationById(int $id, bool $forUpdate = false): Transportation
    {
        $stmt = $this->pdo->prepare('SELECT * FROM transportation WHERE id = ?' . ($forUpdate ? ' FOR UPDATE' : ''));
        if (!$stmt->execute([$id])) {
            throw new DomainRecordNotFoundException();
        }
        $row = $stmt->fetch();
        if (!$row) {
            throw new DomainRecordNotFoundException();
        }
        return self::convertRowToTransportation($row);
    }

    /**
     * @inheritDoc
     * @throws AssertionFailedException
     */
    public function findTransportationOfId(int $id): Transportation
    {
        return $this->findTransportationById($id);
    }

    /**
     * @inheritDoc
     */
    public function createTransportation(Transportation $transportation): Transportation
    {
        $stmt = $this->pdo->prepare('
INSERT INTO transportation (order_id, planned_date, real_date, number, status) 
VALUES (?, ?, ?, ?, ?)');
        $stmt->bindValue(1, $transportation->getOrderId());
        $stmt->bindValue(2, $transportation->getPlannedDate()->format('Y-m-d'));
        $stmt->bindValue(3, $transportation->getRealDate()?->format('Y-m-d'));
        $stmt->bindValue(4, $transportation->getNumber());
        $stmt->bindValue(5, $transportation->getStatus());
        if (!$stmt->execute()) {
            throw new DomainRecordCreationFailureException();
        }
        $transportationId = intval($this->pdo->lastInsertId());
        $transportation->setId($transportationId);
        return $transportation;
    }

    public function updateTransportation(Transportation $old, Transportation $new): Transportation
    {
        $this->pdo->beginTransaction();
        try {
            $realOld = $this->findTransportationById($old->getId(), forUpdate: true);
            if (!$realOld->areSameAttributes($old)) {
                $this->pdo->rollBack();
                return $realOld;
            }
            $stmt = $this->pdo->prepare("UPDATE transportation
SET order_id = ?, planned_date = ?, real_date = ?, number = ?, status = ?
WHERE id = ?");
            $stmt->bindValue(1, $new->getOrderId());
            $stmt->bindValue(2, $new->getPlannedDate()->format('Y-m-d'));
            $stmt->bindValue(3, $new->getRealDate()?->format('Y-m-d'));
            $stmt->bindValue(4, $new->getNumber());
            $stmt->bindValue(5, $new->getStatus());
            $stmt->bindValue(6, $old->getId());
            if (!$stmt->execute()) {
                $this->pdo->rollBack();
                return $old;
            }
            $this->pdo->commit();
            $new->setId($old->getId());
            return $new;
        } catch (Exception|AssertionFailedException) {
            $this->pdo->rollBack();
            return $old;
        }
    }

    public function deleteTransportation(int $transportationId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM transportation WHERE id = ?');
        return $stmt->execute([$transportationId]);
    }
}
