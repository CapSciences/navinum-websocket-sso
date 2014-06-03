<?php

namespace CapSciences\ServerVip\WebsocketBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use CapSciences\ServerVip\WebsocketBundle\Websockets\MessageHandlerCompilerPass;

class CapSciencesServerVipWebsocketBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MessageHandlerCompilerPass());
    }
}
