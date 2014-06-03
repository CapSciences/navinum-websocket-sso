<?php

namespace CapSciences\ServerVip\CoreBundle;

use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CapSciencesServerVipCoreBundle extends Bundle
{

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var $extension SecurityExtension */
        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new DependencyInjection\Security\Factory\NavinumFactory());
    }
}
