<?php
namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\Events;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\Event;

/**
 * This event is used in case the Subscriber needs a service from the DI.
 * It contains the Container from which you can receive your services.
 */
class GetContainerEvent extends Event
{
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}

