<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\DataTransformer;

/**
 * serializes into json and deserializes from json to object
 */
class JsonTransform extends AbstractDataTransformer
{

    /**
     * @inheritdoc
     */
    public function sleepData($data)
    {
        return json_encode($data);
    }

    /**
     * @inheritdoc
     */
    public function wakeupData($data)
    {
        return json_decode($data);
    }
}

