<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue;

use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Tubes\AbstractTube;

/**
 * A QueueWriter is used to put new Jobs in the Queue.
 */
interface QueueWriter
{
    /**
     * Creates a job from the tube and the data-object and puts it in the Queue.
     *
     * @param Tubes\AbstractTube $tube
     * @param $data mixed data-object to put in Queue.
     * @return int job id
     */
    public function put(AbstractTube $tube, $data);
}

