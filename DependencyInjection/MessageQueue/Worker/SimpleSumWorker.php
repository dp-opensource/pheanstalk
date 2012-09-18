<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Worker;

/**
 * A Sample-Implementation of a worker. Calculates a sum and prints it to /tmp/sum.log
 */
class SimpleSumWorker extends AbstractWorker
{
    /**
     * @inheritdoc
     */
    public function work($data, \Pheanstalk\Job $job = null, \Pheanstalk\Pheanstalk $pheanstalk = null)
    {
        file_put_contents('/tmp/sum.log', array_sum($data) . "\n");
    }
}

