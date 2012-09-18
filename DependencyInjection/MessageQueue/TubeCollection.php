<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue;

/**
 * The TubeCollection contains all tubes which are considered by pheanstalk.
 * To register a tube you must tag your tube-service with "pheanstalk.queue.worker".
 */
class TubeCollection
{
    /**
     * @var \SplObjectStorage
     */
    protected $collection;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->collection = new \SplObjectStorage();
    }

    /**
     * @param \DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Tubes\AbstractTube $tube
     * @return void
     */
    public function addTube(\DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Tubes\AbstractTube $tube)
    {
        $this->collection->attach($tube);
    }

    /**
     * @return \SplObjectStorage
     */
    public function getCollection()
    {
        return $this->collection;
    }
}

