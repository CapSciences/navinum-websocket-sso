<?php

namespace CapSciences\ServerVip\CoreBundle\DependencyInjection\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

class NavinumFactory implements SecurityFactoryInterface
{
    protected $options = array(
        'login_path' => '/api/auth',
        'check_path' => '/api/auth',
    );


    public function create(ContainerBuilder $container, $id, $config, $userProviderId, $defaultEntryPointId)
    {
        // authentication provider
        $authProviderId = $this->createAuthProvider($container, $id, $config, $userProviderId);

        // authentication listener
        $listenerId = $this->createListener($container, $id, $config, $userProviderId);

        // create entry point if applicable (optional)
        $entryPointId = $this->createEntryPoint($container, $id, $config, $defaultEntryPointId);

        return array($authProviderId, $listenerId, $entryPointId);
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $providerId = 'security.authentication.provider.navinum.' . $id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('navinum.security.authentication.provider'))
            ->replaceArgument(0, new Reference('navinum_user_provider'));

        return $providerId;
    }

    protected function createEntryPoint($container, $id, $config, $defaultEntryPoint)
    {
        $entryPointId = 'security.authentication.entry_point.navinum.' . $id;
        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('navinum.security.authentication.entry_point'))
            ->addArgument($config['login_path']);

        return $entryPointId;
    }

    protected function createListener($container, $id, $config, $userProvider)
    {
        $listenerId = $this->getListenerId();
        $listener   = new DefinitionDecorator($listenerId);
        $listener->replaceArgument(4, $id);
        $listener->replaceArgument(5, array_intersect_key($config, $this->options));

        $listenerId .= '.' . $id;
        $container->setDefinition($listenerId, $listener);

        return $listenerId;
    }

    public function getPosition()
    {
        return 'form';
    }

    public function getKey()
    {
        return 'navinum';
    }

    public function addConfiguration(NodeDefinition $node)
    {
        $builder = $node->children();

        $builder
            ->scalarNode('provider')->end()
            ->booleanNode('remember_me')->defaultTrue()->end();

        foreach (array_merge($this->options) as $name => $default) {
            if (is_bool($default)) {
                $builder->booleanNode($name)->defaultValue($default);
            } else {
                $builder->scalarNode($name)->defaultValue($default);
            }
        }
    }

    /**
     * Subclasses must return the id of the listener template.
     *
     * Listener definitions should inherit from the AbstractAuthenticationListener
     * like this:
     *
     *    <service id="my.listener.id"
     *             class="My\Concrete\Classname"
     *             parent="security.authentication.listener.abstract"
     *             abstract="true" />
     *
     * In the above case, this method would return "my.listener.id".
     *
     * @return string
     */
    protected function getListenerId()
    {
        return 'navinum.security.authentication.listener';
    }
}