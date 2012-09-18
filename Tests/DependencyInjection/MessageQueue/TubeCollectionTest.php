<?php

namespace DigitalPioneers\PheanstalkBundle\Tests\DependencyInjection\MessageQueue;

use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\TubeCollection;
use DigitalPioneers\PheanstalkBundle\Tests\Helper\MockGenerator;

/**
 * A PHPUnit-testclass to test DigitalPioneers\PheanstalkBundle\MessageQueue\TubeCollection.
 */
class TubeCollectionTest extends \PHPUnit_Framework_TestCase
{
    /* @var TubeCollection */
    protected $tubeCollection;
    /* @var MockGenerator */
    protected $mg;

    public function setUp()
    {
        $this->mg = new MockGenerator($this);
        $this->tubeCollection = new TubeCollection();
    }

    public function testEmptyTube()
    {
        $this->assertEquals(0, $this->tubeCollection->getCollection()->count());
        $this->assertNotEquals(1, $this->tubeCollection->getCollection()->count());
    }

    public function testAddTube()
    {
        $tube = $this->mg->getTubeMock();
        $collection = $this->tubeCollection->getCollection();
        $this->assertFalse(isset($collection[$tube]));
        $this->tubeCollection->addTube($tube);
        $this->assertTrue(isset($collection[$tube]));
    }
}

