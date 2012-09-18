<?php

namespace DigitalPioneers\PheanstalkBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * SymfonyBundle declaration for DigitalPioneersPheanstalkBundle.
 */
class DigitalPioneersPheanstalkBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(
            new \DigitalPioneers\PheanstalkBundle\DependencyInjection\Compiler\TubeLoaderPass()
        );
    }
}

