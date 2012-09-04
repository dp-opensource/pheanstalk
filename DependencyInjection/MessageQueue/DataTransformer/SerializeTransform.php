<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\DataTransformer;

class SerializeTransform extends AbstractDataTransformer
{

    public function sleepData($data)
    {
        return serialize($data);
    }

    public function wakeupData($data)
    {
        return unserialize($data);
    }
}