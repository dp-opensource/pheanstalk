<?php
namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\Subscribers;

use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\PheanstalkEvents;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\TimeEvent;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\GetContainerEvent;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\JobFailedEvent;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\JobSuccessEvent;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\WaitingTimeEvent;
use DigitalPioneers\PheanstalkBundle\DependencyInjection\Events\JobDoneEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAware;

class PheanstalkStatsdSubscriber implements EventSubscriberInterface
{
    /* @var \StatsD\StatsD */
    protected $statsd;
    protected $startedTime;
    protected $jobStartedTime;

    static public function getSubscribedEvents()
    {
        return array(
            PheanstalkEvents::STARTED => array('onStarted', 0),
            PheanstalkEvents::DONE => array('onDone', 0),
            PheanstalkEvents::WAITING_TIME => array('onWaitingTime', 0),
            PheanstalkEvents::JOB_STARTED => array('onJobStarted', 0),
            PheanstalkEvents::JOB_SUCCEEDED => array('onJobSuccess', 0),
            PheanstalkEvents::JOB_FAILED => array('onJobFailed', 0),
            PheanstalkEvents::JOB_DONE => array('onJobDone', 0),
            PheanstalkEvents::GET_CONTAINER => array('setupStatsd', 0)
        );
    }

    public function onStarted(TimeEvent $event)
    {
        $this->statsd->increment('Worker.started');
        $this->startedTime = $event->getTime();
    }

    public function onDone(TimeEvent $event)
    {
        $runningTime = $event->getTime() - $this->startedTime;
        $this->statsd->timing('Worker.time', $runningTime * 1000);
    }

    public function onWaitingTime(WaitingTimeEvent $event)
    {
        $this->statsd->timing('Worker.waiting.time', $event->getWaitingTime());
    }

    public function onJobStarted(TimeEvent $event)
    {
        $this->jobStartedTime = $event->getTime();
    }

    public function onJobSuccess(JobSuccessEvent $event)
    {
        $this->statsd->increment('Worker.jobs.success');
        $this->statsd->increment('Worker.jobs.success.' . $event->getTube());
    }

    public function onJobFailed(JobFailedEvent $event)
    {
        $this->statsd->increment('Worker.jobs.failed');
        $this->statsd->increment('Worker.jobs.failed' . $event->getTube());
        if ($event->isRetried()) {
            $this->statsd->increment('Worker.jobs.failed.retried');
        }else{
            $this->statsd->increment('Worker.jobs.failed.deleted');
        }
    }

    public function onJobDone(JobDoneEvent $event)
    {
        $jobTime = $event->getTime() - $this->jobStartedTime;
        $this->statsd->increment('Worker.jobs.done');
        $this->statsd->increment('Worker.jobs.done.' . $event->getTube());
        $this->statsd->timing('Worker.jobs.time', $jobTime * 1000);
        $this->statsd->timing('Worker.jobs.time.' . $event->getTube(), $jobTime * 1000);
    }

    public function setupStatsd(GetContainerEvent $event)
    {
        $this->statsd = $event->getContainer()->get('statsd');
    }
}