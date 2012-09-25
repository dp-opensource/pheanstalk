<?php

namespace DigitalPioneers\PheanstalkBundle\Tests\DependencyInjection\MessageQueue\DataTransform;

use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\DataTransformer\SerializeTransform;

/**
 * A PHPUnit-testclass to test DigitalPioneers\PheanstalkBundle\MessageQueue\DataTransform\JsonTransformTest.
 */
class SerializeTransformTest extends \PHPUnit_Framework_TestCase
{
    /* @var SerializeTransform */
    protected $dataTransformer;

    public function setUp()
    {
        $this->dataTransformer = new SerializeTransform();
    }

    public function testSleepData()
    {
        $data = new \stdClass();
        $data->leet = 1337;
        $sleepingData = $this->dataTransformer->sleepData($data);
        $this->assertEquals(serialize($data), $sleepingData);
    }

    public function testWakeUpData()
    {
        $data = new \stdClass();
        $data->leet = 1337;
        $sleepingData = serialize($data);
        $wokenUpData = $this->dataTransformer->wakeupData($sleepingData);
        $this->assertEquals($data, $wokenUpData);
    }

    public function testSleepAndWakeUpData()
    {
        $data = new \stdClass();
        $data->leet = 1337;
        $sleepingData = $this->dataTransformer->sleepData($data);
        $wokenUpData = $this->dataTransformer->wakeupData($sleepingData);
        $this->assertEquals($data, $wokenUpData);
    }
}

