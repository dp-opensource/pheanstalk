<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\DataTransformer;

class JsonTransform extends AbstractDataTransformer
{

    public function sleepData($data)
    {
        return json_encode($data);
    }

    public function wakeupData($data)
    {
        return json_decode($data);
    }
}
