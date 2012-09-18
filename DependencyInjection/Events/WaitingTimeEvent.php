<?php
namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * Contains a WaitingTime
 */
class WaitingTimeEvent extends Event
{
    protected $waitingTime;

    /**
     * @param $waitingTime int
     */
    public function __construct($waitingTime)
    {
        $this->waitingTime = $waitingTime;
    }

    /**
     * @return int
     */
    public function getWaitingTime()
    {
        return $this->waitingTime;
    }
}

