<?php
namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\Events;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\Event;

class GetContainerEvent extends Event
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }
}
