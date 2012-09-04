<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Tubes;

abstract class AbstractTube {

    /**
     * Name of the Tube.
     *
     * @abstract
     * @return string
     */
    abstract public function getName();

    /**
     * Jobs with smaller priority values will be
     * scheduled before jobs with larger priorities. The most urgent priority is 0;
     * the least urgent priority is 4,294,967,295.
     *
     * @abstract
     * @return integer
     */
    abstract public function getPriority();

    /**
     * is an integer number of seconds to wait before putting the job in
     * the ready queue. The job will be in the "delayed" state during this time.
     *
     * @abstract
     * @return integer
     */
    abstract public function getDelay();

    /**
     * time to run -- is an integer number of seconds to allow a worker
     * to run this job. This time is counted from the moment a worker reserves
     * this job. If the worker does not delete, release, or bury the job within
     * <ttr> seconds, the job will time out and the server will release the job.
     * The minimum ttr is 1. If the client sends 0, the server will silently
     * increase the ttr to 1.
     *
     * @abstract
     * @return integer
     */
    abstract public function getTtr();

    /**
     * @abstract
     * @return \DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\DataTransformer\AbstractDataTransformer
     */
    abstract public function getDataTransformer();

    /**
     * @abstract
     * @return \DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Worker\AbstractWorker
     */
    abstract public function getWorker();

}