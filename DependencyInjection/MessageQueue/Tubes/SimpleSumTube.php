<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Tubes;

/**
 * This is just an example of how our queueing system works.
 * It takes an simple array and sums up all entrys.
 *
 * This is what an example call would look like:
 *  @example;
 *  $queue = $this->get('pheanstalk.queue');
 *  // @var $queue \DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Pheanstalk
 *  $queue->put($this->get('pheanstalk.queue.tube.simple_sum'), array(2, 5, 10));
 */
class SimpleSumTube extends BaseTube
{

    protected $name = 'simple.sum';

    // Overwriting defaults
    protected $delay = 2;
    protected $priority = 1024;
    protected $tr = 2;
}

