<?php

namespace DigitalPioneers\PheanstalkBundle\Tests\Helper;

use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\DataTransformer\AbstractDataTransformer;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Tubes\AbstractTube;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Worker\AbstractWorker;
use PHPUnit_Framework_TestCase;

/**
 *
 */
class MockGenerator
{
    /* @var PHPUnit_Framework_TestCase */
    protected $context;

    /**
     * @param PHPUnit_Framework_TestCase $context
     */
    public function __construct(PHPUnit_Framework_TestCase $context)
    {
        $this->context = $context;
    }

    /**
     * @return AbstractTube
     */
    public function getTubeMock()
    {
        $tube = $this->context->getMockForAbstractClass(
            '\DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Tubes\AbstractTube'
        );
        $tube->expects($this->context->any())
            ->method('getName')
            ->will($this->context->returnValue('mock.tube'));
        $tube->expects($this->context->any())
            ->method('getPriority')
            ->will($this->context->returnValue(1337));
        $tube->expects($this->context->any())
            ->method('getDelay')
            ->will($this->context->returnValue(42));
        $tube->expects($this->context->any())
            ->method('getTtr')
            ->will($this->context->returnValue(5));
        $tube->expects($this->context->any())
            ->method('getDataTransformer')
            ->will($this->context->returnValue($this->getDataTransformerMock()));
        $tube->expects($this->context->any())
            ->method('getWorker')
            ->will($this->context->returnValue($this->getWorkerMock()));

        return $tube;
    }

    /**
     * @return AbstractWorker
     */
    public function getWorkerMock()
    {
        $worker = $this->context->getMockForAbstractClass(
            '\DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Worker\AbstractWorker'
        );
        $worker->expects($this->context->any())
            ->method('work');
        $worker->expects($this->context->any())
            ->method('processJob');

        return $worker;
    }

    /**
     * @return AbstractDataTransformer
     */
    public function getDataTransformerMock()
    {
        $dataTransformer = $this->context->getMockForAbstractClass(
            '\DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\DataTransformer\AbstractDataTransformer'
        );
        $dataTransformer->expects($this->context->any())
            ->method('sleepData')
            ->will($this->context->returnValue('zzZzzZZz'));
        $dataTransformer->expects($this->context->any())
            ->method('wakeupData')
            ->will($this->context->returnValue('O.O'));

        return $dataTransformer;
    }

    /**
     * @return \Monolog\Logger
     */
    public function getLoggerMock()
    {
        return $this->context->getMockBuilder('\Monolog\Logger')->disableOriginalConstructor()->getMock();
    }

    /**
     * @return \Pheanstalk\Pheanstalk
     */
    public function getPheanstalkMock()
    {
        return $this->context->getMockBuilder('\Pheanstalk\Pheanstalk')->disableOriginalConstructor()->getMock();
    }
}

