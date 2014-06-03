<?php

namespace CapSciences\ServerVip\WebsocketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/monitor")
     * @Template()
     */
    public function monitorAction()
    {
        return array();
    }
}
