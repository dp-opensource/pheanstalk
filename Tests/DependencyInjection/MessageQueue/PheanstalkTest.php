<?php

namespace DigitalPioneers\PheanstalkBundle\Tests\DependencyInjection\MessageQueue;

use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Pheanstalk;
use DigitalPioneers\PheanstalkBundle\Tests\Helper\MockGenerator;
use Pheanstalk\Exception as PheanstalkException;

/**
 * A PHPUnit-testclass to test DigitalPioneers\PheanstalkBundle\MessageQueue\Pheanstalk.
 */
class PheanstalkTest extends \PHPUnit_Framework_TestCase
{
    const DATA = '{ "AnswerToLifeUniverseAndEverythingElse": 42 }';

    /* @var Pheanstalk */
    protected $dpPheanstalk;
    /* @var Pheanstalk/Pheanstalk */
    protected $pheanstalk;
    /* @var MockGenerator */
    protected $mg;
    /* @var \Monolog\Logger */
    protected $logger;

    public function setUp()
    {
        $this->mg = new MockGenerator($this);
        $this->logger = $this->mg->getLoggerMock();
        $this->pheanstalk = $this->mg->getPheanstalkMock();
        $this->dpPheanstalk = new Pheanstalk($this->pheanstalk, $this->logger);
    }

    public function testPut()
    {
        $tube = $this->mg->getTubeMock();

        $workload = array(
            'tube' => $tube->getName(),
            'data' => $tube->getDataTransformer()->sleepData(self::DATA)
        );

        $this->pheanstalk->expects($this->once())
            ->method('put')
            ->with(json_encode($workload), $tube->getPriority(), $tube->getDelay(), $tube->getTtr());

        $this->dpPheanstalk->put($tube, self::DATA);
    }

    public function testFallBack()
    {
        $tube = $this->mg->getTubeMock();
        $tube->getWorker()->expects($this->once())
            ->method('work')
            ->with(self::DATA, null, null);

        $this->pheanstalk->expects($this->once())
            ->method('put')
            ->will($this->throwException(new PheanstalkException()));

        $this->dpPheanstalk->put($tube, self::DATA);
    }

    public function testFallBackFailure()
    {
        $tube = $this->mg->getTubeMock();
        $tube->getWorker()->expects($this->once())
            ->method('work')
            ->will($this->throwException(new \Exception()));

        $this->pheanstalk->expects($this->once())
            ->method('put')
            ->will($this->throwException(new PheanstalkException()));

        $this->logger->expects($this->once())
            ->method('emerg');

        $this->dpPheanstalk->put($tube, self::DATA);
    }
}

