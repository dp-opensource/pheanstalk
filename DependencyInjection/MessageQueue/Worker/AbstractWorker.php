<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Worker;

use Symfony\Component\Console\Output\OutputInterface;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Exceptions\NoRetryException;

abstract class AbstractWorker
{
    abstract public function work($data, \Pheanstalk\Job $job = null, \Pheanstalk\Pheanstalk $pheanstalk = null);

    public function processJob($data, \Pheanstalk\Job $job = null, \Pheanstalk\Pheanstalk $pheanstalk = null, $tubeIdentifier, OutputInterface $output, \Symfony\Bridge\Monolog\Logger $logger) {
        try {
            $this->work($data, $job, $pheanstalk);
            $pheanstalk->delete($job);
            $output->writeln(' done!');
        } catch (NoRetryException $e) {
            $logger->info('NoRetry Exception: ' . $e->getMessage(), $e->getTrace());
            $output->writeln(' Error!');
            $output->writeln(sprintf('Worker [%s] failed (no retry) with: %s', $tubeIdentifier, $e->getMessage()));
            $pheanstalk->delete($job);
        } catch (\Exception $e) {
            $logger->emerg('Exception ' . get_class($e) .': ' . $e->getMessage(), $e->getTrace());
            $output->writeln(' Error!');
            $output->writeln(sprintf('Worker [%s] failed with: %s', $tubeIdentifier, $e->getMessage()));
            $pheanstalk->release($job, 32768 /* just choose a high value for low priority */, 60*3 /* 3 min delay*/);
        }
    }
}
