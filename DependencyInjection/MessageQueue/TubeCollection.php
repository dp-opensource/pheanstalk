<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue;

class TubeCollection
{
    /**
     * @var \SplObjectStorage
     */
    protected $collection;

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
    public function getCollection() {
        return $this->collection;
    }

}
