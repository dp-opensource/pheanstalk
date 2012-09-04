<?php

namespace DigitalPioneers\PheanstalkBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Adds tagged pheanstalk.queue.worker services to tube_collection
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Ole Michaelis <o.michaelis@digitalpioneers.de>
 */
class TubeLoaderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('pheanstalk.queue.tube_collection')) {
            return;
        }

        $definition = $container->getDefinition('pheanstalk.queue.tube_collection');

        // Extensions must always be registered before everything else.
        // For instance, global variable definitions must be registered
        // afterward. If not, the globals from the extensions will never
        // be registered.
        $calls = $definition->getMethodCalls();
        $definition->setMethodCalls(array());
        foreach ($container->findTaggedServiceIds('pheanstalk.queue.worker') as $id => $attributes) {
            $definition->addMethodCall('addTube', array(new Reference($id)));
        }
        $definition->setMethodCalls(array_merge($definition->getMethodCalls(), $calls));
    }
}
