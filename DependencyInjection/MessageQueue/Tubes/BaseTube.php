<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Tubes;

use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\DataTransformer\AbstractDataTransformer;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Worker\AbstractWorker;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Exceptions\TubeException;

/**
 * Base-Implementation of a Tube. You can inherit from BaseTube and modify your tube to your needs.
 */
class BaseTube extends AbstractTube
{

    /**
     * Name of the Tube.
     *
     * @var string
     */
    protected $name = 'default';

    /**
     * @var AbstractDataTransformer
     */
    protected $transform;

    /**
     * @var AbstractWorker
     */
    protected $worker;

    /**
     * Jobs with smaller priority values will be
     * scheduled before jobs with larger priorities. The most urgent priority is 0;
     * the least urgent priority is 4,294,967,295.
     *
     * @var integer
     */
    protected $priority = 1024;

    /**
     * is an integer number of seconds to wait before putting the job in
     * the ready queue. The job will be in the "delayed" state during this time.
     *
     * @var integer
     */
    protected $delay = 0;

    /**
     * time to run -- is an integer number of seconds to allow a worker
     * to run this job. This time is counted from the moment a worker reserves
     * this job. If the worker does not delete, release, or bury the job within
     * <ttr> seconds, the job will time out and the server will release the job.
     * The minimum ttr is 1. If the client sends 0, the server will silently
     * increase the ttr to 1.
     *
     * @var integer
     */
    protected $tr = 10;

    /**
     * how often a job is allowed to retry before it is being discarded
     *
     * @var integer
     */
    protected $maxRetries = 50;

    /**
     * @inheritdoc
     */
    final public function __construct(AbstractDataTransformer $transform, AbstractWorker $worker)
    {
        $this->transform = $transform;
        $this->worker = $worker;
    }

    /**
     * Returns the Name of the Tube
     *
     * @return string
     */
    final public function getName()
    {
        return $this->name;
    }

    /**
     * Returns priority
     *
     * @return integer
     */
    final public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Returns delay
     *
     * @return integer
     */
    final public function getDelay()
    {
        return $this->delay;
    }

    /**
     * Returns tr
     *
     * @return integer
     */
    final public function getTtr()
    {
        return $this->tr;
    }

    /**
     * Returns maxRetries
     *
     * @return int
     */
    final public function getMaxRetries()
    {
        return $this->maxRetries;
    }

    /**
     * Returns data transformer
     *
     * @return AbstractDataTransformer
     * @throws TubeException
     */
    final public function getDataTransformer()
    {
        if (!$this->transform) {
            throw new TubeException('Data transformer not set');
        }
        return $this->transform;
    }

    /**
     * Returns worker
     *
     * @return AbstractWorker
     * @throws TubeException
     */
    final public function getWorker()
    {
        if (!$this->worker) {
            throw new TubeException('Worker not set');
        }
        return $this->worker;
    }

}

