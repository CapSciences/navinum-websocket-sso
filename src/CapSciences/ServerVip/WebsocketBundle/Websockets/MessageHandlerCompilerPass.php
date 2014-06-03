<?php

namespace CapSciences\ServerVip\WebsocketBundle\Websockets;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class MessageHandlerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('servervip2_websocket.websockets.message_handler')) {
            return;
        }

        $definition = $container->getDefinition('servervip2_websocket.websockets.message_handler');

        foreach ($container->findTaggedServiceIds('websocket.handler') as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall('addHandler', array($attributes["namespace"], new Reference($id)));
            }
        }
    }
}