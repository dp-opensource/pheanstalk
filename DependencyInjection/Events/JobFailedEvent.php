<?php
namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\Events;

use Symfony\Component\EventDispatcher\Event;

class JobFailedEvent extends Event
{
    protected $tube;
    protected $retry;

    public function __construct($tube, $retry)
    {
        $this->tube = $tube;
        $this->retry = $retry;
    }

    public function getTube()
    {
        return $this->tube;
    }

    public function isRetried()
    {
        return $this->retry;
    }
}
