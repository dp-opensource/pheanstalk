<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Worker;

class SimpleSumWorker extends AbstractWorker
{

    public function work($data, \Pheanstalk\Job $job = null, \Pheanstalk\Pheanstalk $pheanstalk = null)
    {
        file_put_contents('/tmp/sum.log', array_sum($data) . "\n");
    }
}
