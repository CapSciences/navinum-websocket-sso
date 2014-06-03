<?php

namespace CapSciences\ServerVip\CoreBundle\Controller;

use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Yaml\Parser;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/test")
     * @Template()
     */
    public function testAction()
    {
        return array();
    }

    /**
     * @Route("/test2")
     * @Template()
     */
    public function test2Action()
    {
        return array();
    }

    /**
     * @Route("/sso")
     * @Template()
     */
    public function ssoAction()
    {
        return array();
    }

    /**
     * @Route("/test-push")
     * @Template()
     */
    public function testPushAction()
    {
        // This is our new stuff
        $context = new \ZMQContext();
        $socket  = $context->getSocket(\ZMQ::SOCKET_PUSH, 'my pusher');
        $socket->connect("tcp://127.0.0.1:8001");

        $socket->send(json_encode(array(
            'command' => 'notification.send',
            'data'    => array(
                'dest'   => array('visiteur:FA9BB4C872F45B7EFD93AD5F3B873A8E'),
                'type'   => 'notif:general_information',
                'options' => array(
                    'message' => 'Bravo ! vous venez d\'obtenir le badge tester.',
                )
            )
        )));

        return array();
    }
}
