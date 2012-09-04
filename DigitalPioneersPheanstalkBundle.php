<?php

namespace DigitalPioneers\PheanstalkBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DigitalPioneersPheanstalkBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new \DigitalPioneers\PheanstalkBundle\DependencyInjection\Compiler\TubeLoaderPass());
    }
}
