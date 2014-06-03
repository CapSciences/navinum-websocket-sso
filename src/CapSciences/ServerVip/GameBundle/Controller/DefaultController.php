<?php

namespace CapSciences\ServerVip\GameBundle\Controller;

use CapSciences\ServerVip\GameBundle\Manager\WebServiceManager;
use CapSciences\ServerVip\WebsocketBundle\Providers\Interactif;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        /** @var $provider Interactif */
        $provider = $this->get('cap_sciences_server_vip_navinum.providers.interactif');
        $response = $provider->findBy(array());

        return array('interactfs'=>$response,'nbByLine'=>8);
    }

    /**
     * @Route("/description/{guid}",name="game_description")
     * @Template()
     */
    public function descriptionAction($guid){
        $provider = $this->get('cap_sciences_server_vip_navinum.providers.interactif');
        $response = $provider->find($guid);
        return array('interactif'=>$response);
    }


}
