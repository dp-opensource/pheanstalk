<?php
namespace DigitalPioneers\PheanstalkBundle\Command;

use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\PheanstalkEvents;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\JobDoneEvent;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\JobFailedEvent;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\TimeEvent;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\GetContainerEvent;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\JobMaxRetriesReachedEvent;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\WaitingTimeEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * A CLI to start the workers
 * you can start it with the shell command "app/console pheanstalk:worker {jobsToProcess = null}"
 */
class MessageQueueWorkerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('pheanstalk:worker')
            ->setDescription('Starts an worker for the pheanstalk message queue.')
            ->addArgument(
                'loops',
                InputArgument::OPTIONAL,
                'Defines how many working loops the worker will do before dying.',
                100
            );
    }

    /**
     * The main method getting executed after start
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('loading services');
        $pheanstalk = $this->getContainer()->get('pheanstalk');
        /* @var $pheanstalk \Pheanstalk\Pheanstalk */
        $logger = $this->getContainer()->get('logger');
        /* @var $logger \Symfony\Bridge\Monolog\Logger */
        $dispatcher = new EventDispatcher();
        $dispatcher->dispatch(PheanstalkEvents::GET_CONTAINER, new GetContainerEvent($this->getContainer()));
        $tubeCollection = $this->getContainer()->get('pheanstalk.queue.tube_collection');
        /* @var $tubeCollection \DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\TubeCollection */
        $dispatcher->dispatch(PheanstalkEvents::STARTED, new TimeEvent(microtime(true)));

        $worker = array();
        $dataTransformer = array();
        $maxRetries = array();
        $output->writeln('setting up tubes');
        foreach ($tubeCollection->getCollection() as $tube) {
            /* @var $tube \DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Tubes\AbstractTube */
            $worker[$tube->getName()] = $tube->getWorker();
            $dataTransformer[$tube->getName()] = $tube->getDataTransformer();
            $maxRetries[$tube->getName()] = $tube->getMaxRetries();
            $pheanstalk->watch($tube->getName());
            $output->writeln(sprintf('Watching: %s', $tube->getName()));
        }

        for ($i = 0; $i < $input->getArgument('loops'); $i++) {
            $waitingStarted = microtime(true);
            $output->write(sprintf('Reading for job (#%d)...', $i));
            $job = $pheanstalk->reserve();
            /* @var $job \Pheanstalk\Job*/
            $dispatcher->dispatch(PheanstalkEvents::JOB_STARTED, new TimeEvent(microtime(true)));
            $waitingTime = microtime(true) - $waitingStarted;
            $dispatcher->dispatch(PheanstalkEvents::WAITING_TIME, new WaitingTimeEvent($waitingTime * 1000));
            $workload = json_decode($job->getData());
            $output->write(sprintf(' got a job! [%s] Start heavy computing...', $workload->tube));
            if ($pheanstalk->statsJob($job)->__get('releases') <= $maxRetries[$workload->tube]) {
                $worker[$workload->tube]->processJob(
                    $workload,
                    $dataTransformer[$workload->tube],
                    $job,
                    $pheanstalk,
                    $output,
                    $logger
                );
            } else {
                $pheanstalk->delete($job);
                $message = sprintf(
                    '[Pheanstalk] A job from the tube [%s] has reached maximum retries and has been discarded.',
                    $workload->tube
                );
                $logger->emerg($message);
                $output->writeln(' Error!');
                $output->writeln('Job has reached maximum retries and has been discarded!');
                $dispatcher->dispatch(
                    PheanstalkEvents::JOB_MAX_RETRIES_REACHED,
                    new JobMaxRetriesReachedEvent($workload->tube, $workload, $dataTransformer[$workload->tube])
                );
                $dispatcher->dispatch(PheanstalkEvents::JOB_FAILED, new JobFailedEvent($workload->tube, false));
            }
            $dispatcher->dispatch(PheanstalkEvents::JOB_DONE, new JobDoneEvent($workload->tube, microtime(true)));
        }
        $dispatcher->dispatch(PheanstalkEvents::DONE, new TimeEvent(microtime(true)));
    }
}

