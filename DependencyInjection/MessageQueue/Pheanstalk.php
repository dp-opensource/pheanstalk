<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue;

use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Tubes\AbstractTube;

class Pheanstalk extends AbstractQueue
{

    /**
     * @var \Pheanstalk\Pheanstalk
     */
    protected $pheanstalk;

    /**
     * @var \Monolog\Logger;
     */
    protected $logger;

    /**
     * @param Pheanstalk $pheanstalk
     */
    public function __construct(\Pheanstalk\Pheanstalk $pheanstalk, \Monolog\Logger $logger)
    {
        $this->pheanstalk = $pheanstalk;
        $this->logger = $logger;
    }

    /**
     * Calls worker directly. Fallback when beanstalkd not available.
     *
     * @param AbstractTube $tube
     * @param mixed $data Data which will be transformed to json and put into the mq.
     * @return int job id
     */
    protected function fallBack($tube, $data) {
        try {
            $tube->getWorker()->work($data, null, null);
        } catch (\Exception $e) {
            $this->logger->emerg($e->getMessage(), $e->getTrace());
        }
    }

    /**
     * Puts an Job into the MQ.
     *
     * @param AbstractTube $tube
     * @param mixed $data Data which will be transformed to json and put into the mq.
     * @return int job id
     */
    public function put(AbstractTube $tube, $data)
    {
        $workload = array(
            'tube' => $tube->getName(),
            'data' => $tube->getDataTransformer()->sleepData($data)
        );

        $success = false;
        try {
            $success = $this->pheanstalk->put(json_encode($workload), $tube->getPriority(), $tube->getDelay(), $tube->getTtr());
        } catch (\Pheanstalk\Exception $e) {
            $this->fallBack($tube, $data);
        }
        return $success;
    }
}