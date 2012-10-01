<?php
namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\Events;

use Symfony\Component\EventDispatcher\Event;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\DataTransformer\AbstractDataTransformer;

/**
 * Gets triggered when a job reaches the maximum allowed retries defined in its tube.
 */
class JobMaxRetriesReachedEvent extends Event
{
    protected $tube;
    protected $workload;
    protected $dataTransformer;

    /**
     * @param $tube string The tube-identifier
     * @param $workload string workload of the job
     * @param $dataTransformer AbstractDataTransformer datatransformer of the job
     */
    public function __construct($tube, $workload, $dataTransformer)
    {
        $this->tube = $tube;
        $this->workload = $workload;
        $this->dataTransformer = $dataTransformer;
    }

    /**
     * @return string The tube-identifier
     */
    public function getTube()
    {
        return $this->tube;
    }

    /**
     * @return string The workload
     */
    public function getWorkload()
    {
        return $this->workload;
    }

    /**
     * @return AbstractDataTransformer dataTransformer
     */
    public function getDataTransformer()
    {
        return $this->dataTransformer;
    }
}
