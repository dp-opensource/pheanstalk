<?php
namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\Events;

use Symfony\Component\EventDispatcher\Event;

class JobDoneEvent extends Event
{
    protected $tube;
    protected $time;

    public function __construct($tube, $time)
    {
        $this->tube = $tube;
        $this->time = $time;
    }

    public function getTube()
    {
        return $this->tube;
    }

    public function getTime()
    {
        return $this->time;
    }
}
