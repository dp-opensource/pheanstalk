PheanstalkBundle
================

This symfony-bundle is a simple implementation of the [beanstalkd](http://kr.github.com/beanstalkd/) work queue.

It is designed to decouple time intensive tasks from the user request. The advantage is that the request is processed faster this way, which leads to shorter response times.

## When to use it

The requirements to the System:
* The response has to be independent to the task in order to decouple it.
* Tasks are being processed asynchronously to their requests, that's why tasks and requests shouldn't require to be processed at the same time.
* The Data required to process a job has to be serializable.

### Example

An example use-case is a Notification-System which sends requests in order to dispatch notifications.
In the usual work flow the request would have a longer response time because it has to send an extra request and await its response.

After setting up a [tube](#tubes), [worker](#worker) and [datatransformer](#datatransformers) jobs can be added to the Workqueue as easy as this:
``` php
$queue = $this->get('pheanstalk.queue');
$tube = $this->get('pheanstalk.queue.tube.notifications');
$data = array(
        'recipient' => 'John Doe',
        'message' => 'You have unread messages.'
    );
$queue->put($tube, $data);
```

An example worker implementation could look like this:
``` php
class SimpleSumWorker extends AbstractWorker
{
    protected $notificationsProcessor;

    public function __construct(\MyNamespace\MyNotificationsProcessor $notoficationsProcessor) {
     $this->notificationsProcessor = $notificationsProcessor;
    }

    public function work($data, \Pheanstalk\Job $job = null, \Pheanstalk\Pheanstalk $pheanstalk = null)
    {
        $this->notificationsProcessor->dispatch($data['recipient'], $data['message']);
    }
}
```

To process the jobs you will have to run our worker command with `app/console pheanstalk:worker`.

# Installation
## Symfony 2.0
### I. Download DigitalPioneersPheanstalkBundle and the dependencies

Add the following lines in your `deps` file:

```
[Pheanstalk]
    git=https://github.com/mrpoundsign/pheanstalk.git
    target=/pheanstalk

[drymekPheanstalkBundle]
    git=https://github.com/drymek/PheanstalkBundle.git
    target=/bundles/drymek/PheanstalkBundle

[DigitalPioneersPheanstalkBundle]
    git=git://github.com/digitalpioneers/pheanstalk.git
    target=bundles/DigitalPioneers/PheanstalkBundle
```

Now, run the vendors script to download the bundle:

``` bash
$ php bin/vendors install
```

### II. Configure the Autoloader

Add the `DigitalPioneers`, `Pheanstalk` and `drymek` namespace to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'DigitalPioneers' => __DIR__.'/../vendor/bundles',
    'Pheanstalk'      => __DIR__.'/../vendor/pheanstalk/classes',
    'drymek'          => __DIR__.'/../vendor/bundles',
));
```

### III. Enable the bundles

Finally, enable the bundles by adding them to the AppKernel.php:

``` php
<?php
public function registerBundles()
{
    $bundles = array(
        // ...
        new drymek\PheanstalkBundle\drymekPheanstalkBundle(),
        new DigitalPioneers\PheanstalkBundle\DigitalPioneersPheanstalkBundle(),
    );
}
```

## Symfony 2.1

### I. Install the dependencies
#### A) Register packages as repositories

As the drymek/PheanstalkBundle and our bundle have not yet found their way into packagist you will have to register these as packages first.
Register the `drymek/PheanstalkBundle` and this bundle as repository by adding the following lines to your `composer.json`:
``` json
{
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/digitalpioneers/pheanstalk.git"
        },
        {
            "type": "git",
            "url": "https://github.com/drymek/PheanstalkBundle.git"
        }
    ]
}
```
For a more detailed explanation on vcs-repositories take a look at the [official documentation](http://getcomposer.org/doc/05-repositories.md#vcs).

#### B) Add the bundles

Add our bundle to your `composer.json` by adding this bundle to the require-section of your `composer.json`:

``` json
{
    "require": {
        "digitalpioneers/pheanstalk": "*"
    }
}
```

#### C) Install dependencies

To install our bundle and all dependencies run `php composer.phar install`.

### II. Enable the bundles

Finally, enable the bundles by adding them to the AppKernel.php:

``` php
<?php
public function registerBundles()
{
    $bundles = array(
        // ...
        new drymek\PheanstalkBundle\drymekPheanstalkBundle(),
        new DigitalPioneers\PheanstalkBundle\DigitalPioneersPheanstalkBundle(),
    );
}
```

# Usage
## Tubes and Workers

First of all you have to define Tubes (extending [AbstractTube](https://github.com/digitalpioneers/pheanstalk/blob/master/DependencyInjection/MessageQueue/Tubes/AbstractTube.php)) and Workers (extending [AbstractWorker](https://github.com/digitalpioneers/pheanstalk/blob/master/DependencyInjection/MessageQueue/Worker/AbstractWorker.php)) fitting your use-case.
There is an example [tube](https://github.com/digitalpioneers/pheanstalk/blob/master/DependencyInjection/MessageQueue/Tubes/SimpleSumTube.php) and [worker](https://github.com/digitalpioneers/pheanstalk/blob/master/DependencyInjection/MessageQueue/Worker/SimpleSumWorker.php) contained within this project.

### Registration

You need to register your tubes and worker with the project by adding them to your [services-config-file](https://github.com/digitalpioneers/pheanstalk/blob/master/Resources/config/services.xml).

``` xml
<!-- pheanstalk - Example tube/worker implementation -->
<service id="pheanstalk.queue.worker.simple_sum_worker" class="DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Worker\SimpleSumWorker" />
<service id="pheanstalk.queue.tube.simple_sum" class="DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Tubes\SimpleSumTube">
    <argument type="service" id="pheanstalk.queue.data_transform.json" />
    <argument type="service" id="pheanstalk.queue.worker.simple_sum_worker" />
    <tag name="pheanstalk.queue.worker" />
</service>
```

It is important to tag your tubes with the `pheanstalk.queue.worker`-tag in order to get them loaded.

## Adding jobs to a tube

``` php
$queue = $this->get('pheanstalk.queue');
$tube = $this->get('pheanstalk.queue.tube.simple_sum');
$data = array(2, 5, 10);
$queue->put($tube, $data);
```

## Starting the queue worker

We have written a [command](http://symfony.com/doc/2.0/components/console/introduction.html) which starts the workers. You can use it by running `php app/console pheanstalk:worker`.

By default our command is processing 100 jobs you can modify this number by adding it as an argument. (e.g. `php app/console pheanstalk:worker 42` will process 42 jobs)

# Message Queue System - beanstalkd

## What's here?

A detailed documentation on our Message Queueing system, [beanstalkd](http://kr.github.com/beanstalkd/).

## Overview

The message queue is designed to make cup/time intensive tasks asynchronous to the users request.
This feature can be useful when a task fires a request to a web-service.

## The Components

The system is splitted into 4 different components.

* [queue](#queue) (pheanstalk)
* [tubes](#tubes)
* [workers](#worker)
* [datatransforms](#datatransformers)

Flow:

1. You have to define a Tube. A tube is some kind of a channel where the brokers (task createors) can creating the tasks and the worker will receive them.

2. To define a Tube you'll need a data transform and a worker. The tube has to be defined in the `services.xml` there you'll also find some other examples.

3. To create a task is a clash `$this->queue->put($this->tube, $data);`

4. The worker will receive the data as you give it the the queue here. **IF YOU CHOOSE THE CORRECT DATATRANSFORM**

The workers are running using Symfony2 Commands `app/console dp-pheanstalk:worker [n]` where 'n' defines the number of tasks the worker will complete before he dies, default is 100 jobs. This is necessary b/c of the memory management of php, so we avoid memleaks this way.

### Queue

It's a simple wrapper of the 'pheanstalk' library. Almost no need to make changes here. It enriches the data with some meta informations to enables the worker to chosse the correct tube.

### Tubes

To define a tube you only have to choose a name for it. See a reference implementation. If you have the need of changes the default values you can simple overwrite them. We use getters for this values.

Your create tube has to inherit from `BaseTube` if you want all these developer candy. If you have a very different usecaes you can simply inherit from AbstractTube and set all the values by your own.

```php
<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Tubes;

abstract class AbstractTube {
    abstract public function getName();
    abstract public function getPriority();
    abstract public function getDelay();
    abstract public function getTtr();
    abstract public function getDataTransformer();
    abstract public function getWorker();
}
```

### Worker

The workers in the system does the real job, like talking to the apis or do some calculations.
You have to inherit from AbstractWorker

```php
<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\Worker;

abstract class AbstractWorker {
    abstract public function work($data, \Pheanstalk\Job $job = null, \Pheanstalk\Pheanstalk $pheanstalk = null);

    public function processJob($data, \Pheanstalk\Job $job = null, \Pheanstalk\Pheanstalk $pheanstalk = null, $tubeIdentifier, OutputInterface $output, \Symfony\Bridge\Monolog\Logger $logger) {
        try {
            $this->work($data, $job, $pheanstalk);
            $pheanstalk->delete($job);
            $output->writeln(' done!');
        } catch (NoRetryException $e) {
            $logger->info('NoRetry Exception: ' . $e->getMessage(), $e->getTrace());
            $output->writeln(' Error!');
            $output->writeln(sprintf('Worker [%s] failed (no retry) with: %s', $tubeIdentifier, $e->getMessage()));
            $pheanstalk->delete($job);
        } catch (\Exception $e) {
            $logger->emerg('Exception ' . get_class($e) .': ' . $e->getMessage(), $e->getTrace());
            $output->writeln(' Error!');
            $output->writeln(sprintf('Worker [%s] failed with: %s', $tubeIdentifier, $e->getMessage()));
            $pheanstalk->release($job, 32768 /* just choose a high value for low priority */, 60*3 /* 3 min delay*/);
        }
    }
}
```

#### Exception handling

In the Case that something goes wrong you can throw an Exception in your work-method.
The pre-defined behaviour is that a NoRetryException cancels the job and any other Exception will cause the job to be delayed for 3 minutes.

It can come in handy to customize the Exception handling so we left you the option to change it to your needs. The Exception handling is defined in the processJob-method.

### Datatransformers

The last part in the MQ system are the 'data transformers'. The only job they have to archieve is that the data goes into a queue friendly format and the way back.

Implementing an own DataTransform is in most of the cases not necessary, b/c for the most common use cases there are already the correct transformers:

* JsonTransform
* SerializeTransform

In case you have to implement an own, just inherit from the abstract one:

```php
<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\MessageQueue\DataTransformer;

abstract class AbstractDataTransformer {
    abstract public function sleepData($data);
    abstract public function wakeupData($data);
}
```

Note here, that both methods should retuern the result. So `sleepData()` should return a stringified form of the data. And `wakeupData()` a completly restored structure of the data.
