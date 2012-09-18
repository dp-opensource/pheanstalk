<?php
namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * Gets triggered when a job fails.
 */
class JobFailedEvent extends Event
{
    protected $tube;
    protected $retry;

    /**
     * @param $tube string The tube-identifier
     * @param $retry bool true if the job is retried
     */
    public function __construct($tube, $retry)
    {
        $this->tube = $tube;
        $this->retry = $retry;
    }

    /**
     * @return string The tube-identifier
     */
    public function getTube()
    {
        return $this->tube;
    }

    /**
     * @return bool
     */
    public function isRetried()
    {
        return $this->retry;
    }
}

