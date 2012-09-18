<?php
namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * Gets triggered when a job succeeds.
 */
class JobSuccessEvent extends Event
{
    protected $tube;

    /**
     * @param $tube string The tube-identifier
     */
    public function __construct($tube)
    {
        $this->tube = $tube;
    }

    /**
     * @return string The tube-identifier
     */
    public function getTube()
    {
        return $this->tube;
    }
}
