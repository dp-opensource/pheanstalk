<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Exceptions;

/**
* NoRetryException
*
* This exception can be thrown by a worker when the job fails and
* the job should NOT be processed again
*/
class NoRetryException extends \Exception
{
}

