<?php
namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\Events;

use Symfony\Component\EventDispatcher\Event;

class WaitingTimeEvent extends Event
{
    protected $waitingTime;

    public function __construct($waitingTime)
    {
        $this->waitingTime = $waitingTime;
    }

    public function getWaitingTime()
    {
        return $this->waitingTime;
    }
}
