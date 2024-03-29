<?php

declare(strict_types=1);

namespace Building\Domain\DomainEvent;

use Prooph\EventSourcing\AggregateChanged;
use Rhumsaa\Uuid\Uuid;

final class UserCheckedIn extends AggregateChanged
{
    public static function fromBuildingIdAndUsername(
        Uuid $buildingId,
        string $name
    ) : self {
        return self::occur((string) $buildingId, ['username' => $name]);

    }

    public function username() : string
    {
        return $this->payload['username'];
    }
}
