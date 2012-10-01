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
 * The PheanstalkStatsdListener has to be subscribed to PheanstalkEvents properly in order to fill statsd with
 * information.
 *
 * To use the PheanstalkStatsdListener you will have to install StatsD!
 */
class PheanstalkStatsdListener
{
    /* @var \StatsD\StatsD */
    protected $statsd;
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
        $this->statsd->increment('Worker.started');
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
        $this->statsd->timing('Worker.time', $runningTime * 1000);
    }

    /**
     * Adds a Waiting-Time (as "Worker.waiting.time") to statistics.
     * Gets triggered when the worker has had a waiting time in which he was idle.
     *
     * @param \DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\WaitingTimeEvent $event
     */
    public function onWaitingTime(WaitingTimeEvent $event)
    {
        $this->statsd->timing('Worker.waiting.time', $event->getWaitingTime());
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
        $this->statsd->increment('Worker.jobs.success');
        $this->statsd->increment('Worker.jobs.success.' . $event->getTube());
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
        $this->statsd->increment('Worker.jobs.failed');
        $this->statsd->increment('Worker.jobs.failed.' . $event->getTube());
        if ($event->isRetried()) {
            $this->statsd->increment('Worker.jobs.failed.retried');
        } else {
            $this->statsd->increment('Worker.jobs.failed.deleted');
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
        $this->statsd->increment('Worker.jobs.done');
        $this->statsd->increment('Worker.jobs.done.' . $event->getTube());
        $this->statsd->timing('Worker.jobs.time', $jobTime * 1000);
        $this->statsd->timing('Worker.jobs.time.' . $event->getTube(), $jobTime * 1000);
    }

    /**
     * Uses the Container from the Event to initialize statsd.
     *
     * @param \DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\GetContainerEvent $event
     */
    public function setupStatsd(GetContainerEvent $event)
    {
        $this->statsd = $event->getContainer()->get('statsd');
    }
}

