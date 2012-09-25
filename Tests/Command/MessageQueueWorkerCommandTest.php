<?php

namespace DigitalPioneers\PheanstalkBundle\Tests\Command;

use DigitalPioneers\PheanstalkBundle\Command\MessageQueueWorkerCommand;
use DigitalPioneers\PheanstalkBundle\Tests\Helper\MockGenerator;
use Pheanstalk\Pheanstalk;
use Pheanstalk\Job;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Kernel;

/**
 * A PHPUnit-testclass to test DigitalPioneers\PheanstalkBundle\MessageQueue\TubeCollection.
 */
class MessageQueueWorkerCommandTest extends \PHPUnit_Framework_TestCase
{
    /* @var MockGenerator */
    protected $mg;
    /* @var Pheanstalk */
    protected $pheanstalk;
    /* @var Container */
    protected $container;
    /* @var Application */
    protected $app;
    /* @var Command */
    protected $command;
    /* @var CommandTester */
    protected $commandTester;

    public function setUp()
    {
        $this->mg = new MockGenerator($this);

        $this->pheanstalk = $this->mg->getPheanstalkMock();

        $this->container = $this->getContainer($this->mg, $this->pheanstalk);

        $this->command = new MessageQueueWorkerCommand();
        $this->command->setContainer($this->container);

        $this->commandTester = new CommandTester($this->command);
    }

    public function testNormalCommandCall()
    {
        $data = new \stdClass();
        $data->tube = 'mock.tube';
        $data->data = 'O.O';
        $job = new Job(1, json_encode($data));

        $this->pheanstalk->expects($this->any())
            ->method('reserve')
            ->will($this->returnValue($job));

        // worker which will expect the "work"-method to be called exactly 100 times
        $worker = $this->mg->getWorkerMock();
        $worker->expects($this->exactly(100))
            ->method('work')
            ->with($data->data, $job, $this->pheanstalk);

        $this->container->set(
            'pheanstalk.queue.tube_collection',
            $this->mg->getTubeColletionMock(array($this->mg->getTubeMock($worker)))
        );

        $this->commandTester->execute(array());
    }

    public function testCommandCallWithArguments()
    {
        $data = new \stdClass();
        $data->tube = 'mock.tube';
        $data->data = 'O.O';
        $job = new Job(1, json_encode($data));

        $this->pheanstalk->expects($this->any())
            ->method('reserve')
            ->will($this->returnValue($job));

        // worker which will expect the "work"-method to be called exactly 100 times
        $worker = $this->mg->getWorkerMock();
        $worker->expects($this->exactly(42))
            ->method('work')
            ->with($data->data, $job, $this->pheanstalk);

        $this->container->set(
            'pheanstalk.queue.tube_collection',
            $this->mg->getTubeColletionMock(array($this->mg->getTubeMock($worker)))
        );

        $this->commandTester->execute(array('loops' => 42));
    }

    /**
     * Returns a valid Container
     *
     * @return \Symfony\Component\DependencyInjection\Container
     */
    protected function getContainer(MockGenerator $mg, Pheanstalk $pheanstalk)
    {
        $container = new Container();

        $container->setParameter('kernel.root_dir', sys_get_temp_dir());

        $container->set('pheanstalk', $pheanstalk);
        $container->set('logger', $mg->getLoggerMock());
        $container->set(
            'pheanstalk.queue.tube_collection',
            $this->mg->getTubeColletionMock(array($mg->getTubeMock()))
        );

        return $container;
    }
}

