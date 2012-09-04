<?php
namespace DigitalPioneers\PheanstalkBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MessageQueueWorkerCommand extends ContainerAwareCommand {
    protected function configure() {
        $this->setName('dp-pheanstalk:worker')
            ->setDescription('Starts an worker for the pheanstalk message queue.')
            ->addArgument('loops', InputArgument::OPTIONAL, 'Defines how many working loops the worker will do before dying.', 100);
    }
    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln('loading services');
        $pheanstalk = $this->getContainer()->get('pheanstalk'); /* @var $pheanstalk \Pheanstalk\Pheanstalk */
        $logger = $this->getContainer()->get('logger'); /* @var $logger \Symfony\Bridge\Monolog\Logger */
        $tubeCollection = $this->getContainer()->get('pheanstalk.queue.tube_collection'); /* @var $tubeCollection \DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\TubeCollection */

        $worker = array();
        $dataTransformer = array();
        $output->writeln('setting up tubes');
        foreach ($tubeCollection->getCollection() as $tube) { /* @var $tube \DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Tubes\AbstractTube */
            $worker[$tube->getName()] = $tube->getWorker();
            $dataTransformer[$tube->getName()] = $tube->getDataTransformer();
            $pheanstalk->watch($tube->getName());
            $output->writeln(sprintf('Watching: %s', $tube->getName()));
        }

        for ($i = 0; $i < $input->getArgument('loops'); $i++) {
            $output->write(sprintf('Reading for job (#%d)...', $i));
            $job = $pheanstalk->reserve(); /* @var $job \Pheanstalk\Job*/
            $workload = json_decode($job->getData());
            $output->write(sprintf(' got a job! [%s] Start heavy computing...', $workload->tube));
            $worker[$workload->tube]->processJob($dataTransformer[$workload->tube]->wakeupData($workload->data), $job, $pheanstalk, $workload->tube, $output, $logger);
        }
    }
}
