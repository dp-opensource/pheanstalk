<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\DataTransformer;

/**
 * An Abstract class for the DataTransformer.
 * A DataTransformer is used for serialization and deserialization of Objects.
 */
abstract class AbstractDataTransformer
{

    /**
     * serializes (sleeps) a given data-object
     *
     * @param $data mixed data-object to serialize
     * @return string serialized output
     */
    abstract public function sleepData($data);

    /**
     * deserializes (wakes up) a given string to a data-object
     *
     * @param $data string serialized data-object to deserialize
     * @return mixed woken up data-object
     */
    abstract public function wakeupData($data);
}

