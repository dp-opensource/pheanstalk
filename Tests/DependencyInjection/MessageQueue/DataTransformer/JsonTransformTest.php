<?php

namespace DigitalPioneers\PheanstalkBundle\Tests\DependencyInjection\MessageQueue\DataTransform;

use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\DataTransformer\JsonTransform;

/**
 * A PHPUnit-testclass to test DigitalPioneers\PheanstalkBundle\MessageQueue\DataTransform\JsonTransformTest.
 */
class JsonTransformTest extends \PHPUnit_Framework_TestCase
{
    /* @var JsonTransform */
    protected $dataTransformer;

    public function setUp()
    {
        $this->dataTransformer = new JsonTransform();
    }

    public function testSleepData()
    {
        $data = new \stdClass();
        $data->leet = 1337;
        $sleepingData = $this->dataTransformer->sleepData($data);
        $this->assertEquals(json_encode($data), $sleepingData);
    }

    public function testWakeUpData()
    {
        $data = new \stdClass();
        $data->leet = 1337;
        $sleepingData = json_encode($data);
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

