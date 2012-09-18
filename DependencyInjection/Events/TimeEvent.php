<?php
namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * Contains a Time which is determined by "microtime(true)"
 */
class TimeEvent extends Event
{
    protected $time;

    /**
     * @param $time int
     */
    public function __construct($time)
    {
        $this->time = $time;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }
}

