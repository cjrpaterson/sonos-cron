<?php

namespace SonosCron;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers services tagged as 'sonos_cron.handler' with the QueueProcessor
 *
 * @author Chris Paterson
 */
class HandlerCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('sonos_cron.processor')) {
            return;
        }

        $definition = $container->findDefinition('sonos_cron.processor');
        $taggedServices = $container->findTaggedServiceIds('sonos_cron.handler');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('registerHandler', array(new Reference($id)));
        }
    }
}
