<?php
namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Worker;

use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\PheanstalkEvents;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\DataTransformer\AbstractDataTransformer;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\JobFailedEvent;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\JobSuccessEvent;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Exceptions\NoRetryException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * An Abstract class of a Worker.
 * Workers are processing jobs.
 */
abstract class AbstractWorker
{
    /**
     * main method to process a job.
     *
     * @param $data mixed the data which is necessary to process the job
     * @param \Pheanstalk\Job $job
     * @param \Pheanstalk\Pheanstalk $pheanstalk
     * @return mixed
     */
    abstract public function work($data, \Pheanstalk\Job $job = null, \Pheanstalk\Pheanstalk $pheanstalk = null);

    /**
     * the actual method to process a job. You can override this method in order to define a custom exception-handling
     *
     * @param $data mixed the data which is necessary to process the job
     * @param \Pheanstalk\Job $job
     * @param \Pheanstalk\Pheanstalk $pheanstalk
     * @param $tubeIdentifier string the name of the tube
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \Symfony\Bridge\Monolog\Logger $logger
     */
    public function processJob(
        $workload,
        AbstractDataTransformer $dataTransformer,
        \Pheanstalk\Job $job,
        \Pheanstalk\Pheanstalk $pheanstalk,
        OutputInterface $output,
        \Symfony\Bridge\Monolog\Logger $logger
    ) {
        $data = $dataTransformer->wakeupData($workload->data);
        $tubeIdentifier = $workload->tube;
        $dispatcher = new EventDispatcher();
        try {
            $this->work($data, $job, $pheanstalk);
            $pheanstalk->delete($job);
            $output->writeln(' done!');
            $dispatcher->dispatch(PheanstalkEvents::JOB_SUCCEEDED, new JobSuccessEvent($tubeIdentifier));
        } catch (NoRetryException $e) {
            $logger->info('NoRetry Exception: ' . $e->getMessage(), $e->getTrace());
            $output->writeln(' Error!');
            $output->writeln(sprintf('Worker [%s] failed (no retry) with: %s', $tubeIdentifier, $e->getMessage()));
            $pheanstalk->delete($job);
            $dispatcher->dispatch(PheanstalkEvents::JOB_FAILED, new JobFailedEvent($tubeIdentifier, false));
        } catch (\Exception $e) {
            $logger->emerg('Exception ' . get_class($e) . ': ' . $e->getMessage(), $e->getTrace());
            $output->writeln(' Error!');
            $output->writeln(sprintf('Worker [%s] failed with: %s', $tubeIdentifier, $e->getMessage()));
            $pheanstalk->release($job, 32768 /* just choose a high value for low priority */, 60 * 3 /* 3 min delay*/);
            $dispatcher->dispatch(PheanstalkEvents::JOB_FAILED, new JobFailedEvent($tubeIdentifier, true));
        }
    }
}

