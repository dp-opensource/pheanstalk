<?php
namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\Listener;

use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\TimeEvent;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\GetContainerEvent;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\JobFailedEvent;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\JobSuccessEvent;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\WaitingTimeEvent;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\JobDoneEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * This is an example which demonstrates how to use the PheanstalkEvents to save statistics about the Worker.
 * It is not functional and is not ready for use.
 * The PheanstalkStatisticsListener has to be subscribed to PheanstalkEvents in order to work properly.
 */
class PheanstalkStatisticsListener
{
    protected $statisticsClient;
    protected $startedTime;
    protected $jobStartedTime;

    /**
     * Increments "worker.started" in statistics.
     * Gets triggered when the worker is started.
     *
     * @param \DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\TimeEvent $event
     */
    public function onStarted(TimeEvent $event)
    {
        $this->statisticsClient[] = "->increment('Worker.started')";
        $this->startedTime = $event->getTime();
    }

    /**
     * Adds a Worker-running time (as "Worker.time") to statistics.
     * Gets triggered when the worker is done.
     *
     * @param \DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\TimeEvent $event
     */
    public function onDone(TimeEvent $event)
    {
        $runningTime = $event->getTime() - $this->startedTime;
        $this->statisticsClient[] = "->timing('Worker.time', $runningTime * 1000)";
    }

    /**
     * Adds a Waiting-Time (as "Worker.waiting.time") to statistics.
     * Gets triggered when the worker has had a waiting time in which he was idle.
     *
     * @param \DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\WaitingTimeEvent $event
     */
    public function onWaitingTime(WaitingTimeEvent $event)
    {
        $this->statisticsClient[] = "->timing('Worker.waiting.time', $event->getWaitingTime())";
    }

    /**
     * Saves the time when a job was started to a local variable.
     * Gets triggered when a job is started.
     *
     * @param \DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\TimeEvent $event
     */
    public function onJobStarted(TimeEvent $event)
    {
        $this->jobStartedTime = $event->getTime();
    }

    /**
     * Increases "Worker.jobs.success" and "Worker.jobs.success.{tube-identifier}" in statistics.
     * Gets triggered when a job successfully finishes.
     *
     * @param \DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\JobSuccessEvent $event
     */
    public function onJobSuccess(JobSuccessEvent $event)
    {
        $this->statisticsClient[] = "->increment('Worker.jobs.success')";
        $this->statisticsClient[] = "->increment('Worker.jobs.success.' . $event->getTube())";
    }

    /**
     * Increases "Worker.jobs.failed", "Worker.jobs.failed.{tube-identifier}" and "Worker.jobs.failed.{retried|deleted}"
     * (depending on whether the job has been retried or not) in statistics.
     * Gets triggered when a job fails.
     *
     * @param \DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\JobFailedEvent $event
     */
    public function onJobFailed(JobFailedEvent $event)
    {
        $this->statisticsClient[] = "->increment('Worker.jobs.failed')";
        $this->statisticsClient[] = "->increment('Worker.jobs.failed.' . $event->getTube())";
        if ($event->isRetried()) {
            $this->statisticsClient[] = "->increment('Worker.jobs.failed.retried')";
        } else {
            $this->statisticsClient[] = "->increment('Worker.jobs.failed.deleted')";
        }
    }

    /**
     * Increases "Worker.jobs.max_retries" and "Worker.jobs.max_retries.{tube-identifier}"
     * Gets triggered when a job reaches its maximum retries defined by the tube
     *
     * @param \DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\JobFailedEvent $event
     */
    public function onMaxRetriesReached(JobFailedEvent $event)
    {
        $this->statisticsClient[] = "->increment('Worker.jobs.max_retries')";
        $this->statisticsClient[] = "->increment('Worker.jobs.max_retries.' . $event->getTube())";
    }

    /**
     * Increases "Worker.jobs.done", "Worker.jobs.done.{tube-identifier} and adds the running-time of the job as
     * "Worker.jobs.time" and "Worker.jobs.time.{tube-identifier}" to statistics.
     * Gets triggered when a job is done.
     *
     * @param \DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\JobDoneEvent $event
     */
    public function onJobDone(JobDoneEvent $event)
    {
        $jobTime = $event->getTime() - $this->jobStartedTime;
        $this->statisticsClient[] = "->increment('Worker.jobs.done')";
        $this->statisticsClient[] = "->increment('Worker.jobs.done.'" . $event->getTube() . ")";
        $this->statisticsClient[] = "->timing('Worker.jobs.time', . $jobTime * 1000)";
        $this->statisticsClient[] = "->timing('Worker.jobs.time.' . $event->getTube(), $jobTime * 1000)";
    }

    /**
     * Uses the Container from the Event to initialize the statistics client.
     *
     * @param \DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\GetContainerEvent $event
     */
    public function setupStatisticsClient(GetContainerEvent $event)
    {
        //$this->statisticsClient = $event->getContainer()->get('statisticsClient');
        $this->statisticsClient = array();
    }
}

