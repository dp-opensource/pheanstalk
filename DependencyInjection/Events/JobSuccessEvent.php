<?php
namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\Events;

use Symfony\Component\EventDispatcher\Event;

class JobSuccessEvent extends Event
{
    protected $tube;

    public function __construct($tube)
    {
        $this->tube = $tube;
    }

    public function getTube()
    {
        return $this->tube;
    }
}
