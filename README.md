PheanstalkBundle
================

A simple pheanstalk-bundle for Symfony

#Installation
## Symfony 2.0
### A) Download DigitalPioneersPheanstalkBundle and the dependencies
Add the following lines in your `deps` file:

```
[Pheanstalk]
    git=https://github.com/mrpoundsign/pheanstalk.git
    target=/pheanstalk

[drymekPheanstalkBundle]
    git=https://github.com/drymek/PheanstalkBundle.git
    target=/bundles/drymek/PheanstalkBundle

[DigitalPioneersPheanstalkBundle]
    git=git://github.com/Senci/PheanstalkBundle.git
    target=bundles/DigitalPioneers/PheanstalkBundle
```

Now, run the vendors script to download the bundle:

``` bash
$ php bin/vendors install
```

### B) Configure the Autoloader

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

### C) Enable the bundle

Finally, enable the bundles in the kernel:

``` php
<?php
// app/AppKernel.php

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

TODO

#Message Queue System - beanstalkd

##What's here?

A detailed documentation on our Message Queueing system, [beanstalkd](http://kr.github.com/beanstalkd/).

##Overview

The message queue is designed to make cup/time intensive tasks asynchronous to the users request.
This feature can be useful when a task fires a request to a web-service.

##The Components

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

###Queue

It's a simple wrapper of the 'pheanstalk' library. Almost no need to make changes here. It enriches the data with some meta informations to enables the worker to chosse the correct tube.

###Tubes

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

###Worker

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

####Exception handling

In the Case that something goes wrong you can throw an Exception in your work-method.
The pre-defined behaviour is that a NoRetryException cancels the job and any other Exception will cause the job to be delayed for 3 minutes.

It can come in handy to customize the Exception handling so we left you the option to change it to your needs. The Exception handling is defined in the processJob-method.

###Datatransformers

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