<?php
namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * Gets triggered when a job is done.
 */
class JobDoneEvent extends Event
{
    protected $tube;
    protected $time;

    /**
     * @param $tube string The tube-identifier
     * @param $time int The time when the job was started
     */
    public function __construct($tube, $time)
    {
        $this->tube = $tube;
        $this->time = $time;
    }

    /**
     * @return string the tube identifier
     */
    public function getTube()
    {
        return $this->tube;
    }

    /**
     * @return int The time when the job was started
     */
    public function getTime()
    {
        return $this->time;
    }
}

