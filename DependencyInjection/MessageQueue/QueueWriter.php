<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue;

use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Tubes\AbstractTube;

interface QueueWriter {
    public function put(AbstractTube $tube, $data);
}