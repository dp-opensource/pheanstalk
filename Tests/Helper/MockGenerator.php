<?php

namespace DigitalPioneers\PheanstalkBundle\Tests\Helper;

use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\DataTransformer\AbstractDataTransformer;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\TubeCollection;
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
     * @param AbstractWorker $worker will return a specific worker when given
     * @return AbstractTube
     */
    public function getTubeMock($worker = null)
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
        if (isset($worker)) {
            $tube->expects($this->context->any())
                ->method('getWorker')
                ->will($this->context->returnValue($worker));
        } else {
            $tube->expects($this->context->any())
                ->method('getWorker')
                ->will($this->context->returnValue($this->getWorkerMock()));
        }

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
     * @return \Symfony\Bridge\Monolog\Logger
     */
    public function getLoggerMock()
    {
        return $this->context->getMockBuilder('\Symfony\Bridge\Monolog\Logger')
            ->disableOriginalConstructor()->getMock();
    }

    /**
     * @return \Pheanstalk\Pheanstalk
     */
    public function getPheanstalkMock()
    {
        $arrayResponse = $this->context->getMockBuilder('\Pheanstalk\Response\ArrayResponse')
            ->disableOriginalConstructor()->getMock();
        $arrayResponse->expects($this->context->any())
            ->method('__get')
            ->will($this->context->returnValue(0));
        $pheanstalk = $this->context->getMockBuilder('\Pheanstalk\Pheanstalk')->disableOriginalConstructor()->getMock();
        $pheanstalk->expects($this->context->any())
            ->method('statsJob')
            ->will($this->context->returnValue($arrayResponse));
        return $pheanstalk;
    }

    /**
     * @param array $tubes all tubes which should be registered witin the TubeCollection
     * @return TubeCollection
     */
    public function getTubeColletionMock($tubes)
    {
        $tubeCollection = $this->context->getMockBuilder(
            '\DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\TubeCollection'
        )->disableOriginalConstructor()->getMock();

        $collection = new \SplObjectStorage();
        foreach ($tubes as $tube) {
            $collection->attach($tube);
        }
        $tubeCollection->expects($this->context->any())
            ->method('getCollection')
            ->will($this->context->returnValue($collection));

        return $tubeCollection;
    }
}

