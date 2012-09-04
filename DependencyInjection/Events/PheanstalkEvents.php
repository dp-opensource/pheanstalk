<?php
namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\Events;

final class PheanstalkEvents {
    /**
     * The pheanstalk.started event is thrown each time the MessageQueueWorkerCommand is started.
     */
    const STARTED = 'pheanstalk.started';
    /**
     * The pheanstalk.waiting_time event is thrown each time the waiting time for a new job is over.
     */
    const WAITING_TIME = 'pheanstalk.waiting_time';
    /**
     * The pheanstalk.jobs.started event is thrown each time a job is started.
     */
    const JOB_STARTED = 'pheanstalk.jobs.started';
    /**
     * The pheanstalk.jobs.success event is thrown each time a job succeeds.
     */
    const JOB_SUCCEEDED = 'pheanstalk.jobs.success';
    /**
     * The pheanstalk.jobs.failed event is thrown each time a job fails.
     */
    const JOB_FAILED = 'pheanstalk.jobs.failed';
    /**
     * The pheanstalk.jobs.done event is thrown each time a job is done.
     */
    const JOB_DONE = 'pheanstalk.jobs.done';
    /**
     * The pheanstalk.done event is thrown each time the MessageQueueWorkerCommand is done.
     */
    const DONE = 'pheanstalk.done';
    /**
     * You can throw the pheanstalk.container event in case you need the service container in your Subscriber.
     */
    const GET_CONTAINER = 'pheanstalk.get_container';
}