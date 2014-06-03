<?php


namespace CapSciences\ServerVip\CoreBundle;


use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Routing\Router;

abstract class Mailer
{

    /**
     * @var Container
     */
    protected $container;

    protected $sender;

    function __construct(Container $container, $sender)
    {
        $this->container = $container;
        $this->sender = $sender;
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string  $route      The name of the route
     * @param mixed   $parameters An array of parameters
     * @param Boolean $absolute   Whether to generate an absolute URL
     *
     * @return string The generated URL
     */
    protected function generateUrl($route, $parameters = array(), $absolute = false)
    {
        return $this->getRouter()->generate($route, $parameters, $absolute);
    }

    /**
     * Send message
     *
     * @param $message
     */
    protected function send(\Swift_Message $message)
    {
        $message->setFrom($this->sender);
        $this->getMailer()->send($message);
    }

    /**
     * Returns a rendered view.
     *
     * @param string $view       The view name
     * @param array  $parameters An array of parameters to pass to the view
     *
     * @return string The renderer view
     */
    protected function renderView($view, array $parameters = array())
    {
        return $this->getTemplating()->render($view, $parameters);
    }

    /**
     * @return \Swift_Mailer
     */
    private function getMailer()
    {
        return $this->container->get('mailer');
    }

    /**
     * @return Router
     */
    private function getRouter()
    {
        return $this->container->get('router');
    }

    /**
     * @return \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    private function getTemplating()
    {
        return $this->container->get('templating');
    }
}