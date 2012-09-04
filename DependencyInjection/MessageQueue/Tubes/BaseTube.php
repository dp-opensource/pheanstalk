<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Tubes;

use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\DataTransformer\AbstractDataTransformer;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Worker\AbstractWorker;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Exceptions\TubeException;

class BaseTube extends AbstractTube {

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

    public final function __construct(AbstractDataTransformer $transform, AbstractWorker $worker)
    {
        $this->transform = $transform;
        $this->worker = $worker;
    }

    /**
     * Returns the Name of the Tube
     *
     * @return string
     */
    public final function getName()
    {
        return $this->name;
    }

    /**
     * Returns priority
     *
     * @return integer
     */
    public final function getPriority()
    {
        return $this->priority;
    }

    /**
     * Returns delay
     *
     * @return integer
     */
    public final function getDelay()
    {
        return $this->delay;
    }

    /**
     * Returns tr
     *
     * @return integer
     */
    public final function getTtr()
    {
        return $this->tr;
    }

    /**
     * Returns data transformer
     *
     * @return \DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\DataTransformer\AbstractDataTransformer
     */
    public final function getDataTransformer()
    {
        if(!$this->transform) {
            throw new TubeException('Data transformer not set');
        }
        return $this->transform;
    }

    /**
     * Returns worker
     *
     * @return \DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Worker\AbstractWorker
     */
    public final function getWorker()
    {
        if(!$this->worker) {
            throw new TubeException('Worker not set');
        }
        return $this->worker;
    }

}