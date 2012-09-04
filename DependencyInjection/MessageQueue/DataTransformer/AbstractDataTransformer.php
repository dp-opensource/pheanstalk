<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\DataTransformer;

abstract class AbstractDataTransformer
{

    abstract public function sleepData($data);
    abstract public function wakeupData($data);

}