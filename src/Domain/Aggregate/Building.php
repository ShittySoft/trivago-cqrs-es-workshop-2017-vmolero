<?php

declare(strict_types=1);

namespace Building\Domain\Aggregate;

use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\UserCheckedIn;
use Building\Domain\DomainEvent\UserCheckedOut;
use Prooph\EventSourcing\AggregateRoot;
use Rhumsaa\Uuid\Uuid;

final class Building extends AggregateRoot
{
    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array<string, null>
     */
    private $checkedInUsers = [];

    public static function new(string $name) : self
    {
        $self = new self();

        $self->recordThat(NewBuildingWasRegistered::fromBuildingIdAndName(
            Uuid::uuid4(),
            $name
        ));

        return $self;
    }

    public function checkInUser(string $username)
    {
        if (in_array($username, $this->checkedInUsers)) {
            throw new \DomainException(sprintf("User %s alreadz exists into building: %s", $username, $this->aggregateId()));
        }
        // Domain checks are performed here, into the aggregator.
        $this->recordThat(UserCheckedIn::fromBuildingIdAndUsername(
            $this->uuid,
            $username
        ));
    }

    public function checkOutUser(string $username)
    {
        $this->recordThat(UserCheckedOut::fromBuildingIdAndUsername(
            $this->uuid,
            $username
        ));
    }

    public function whenNewBuildingWasRegistered(NewBuildingWasRegistered $event)
    {
        $this->uuid = $event->uuid();
        $this->name = $event->name();
    }

    public function whenUserCheckedIn(UserCheckedIn $event)
    {
        // push to an array
        $this->checkedInUsers[$event->username()] = null;
    }

    public function whenUserCheckedOut(UserCheckedOut $event)
    {
        unset($this->checkedUsers[$event->username()]);
    }

    /**
     * {@inheritDoc}
     */
    protected function aggregateId() : string
    {
        return (string) $this->uuid;
    }

    /**
     * {@inheritDoc}
     */
    public function id() : string
    {
        return $this->aggregateId();
    }
}
