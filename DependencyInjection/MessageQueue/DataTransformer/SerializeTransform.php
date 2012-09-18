<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\DataTransformer;

/**
 * Serializes/Deserializes data with "serialize($data)"/"unserialize($data)" methods.
 */
class SerializeTransform extends AbstractDataTransformer
{

    /**
     * @inheritdoc
     */
    public function sleepData($data)
    {
        return serialize($data);
    }

    /**
     * @inheritdoc
     */
    public function wakeupData($data)
    {
        return unserialize($data);
    }
}

