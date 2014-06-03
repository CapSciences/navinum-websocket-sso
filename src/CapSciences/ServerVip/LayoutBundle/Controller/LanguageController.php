<?php

namespace CapSciences\ServerVip\LayoutBundle\Controller;

use CapSciences\ServerVip\NavinumBundle\Model\Langue;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class LanguageController extends Controller
{
    /**
     * @Route("/change/{_locale}")
     */
    public function changeAction()
    {
        $this->getRequest()->getSession()->set('_locale', $this->getRequest()->getLocale());

        $url = $this->getRequest()->headers->get('referer');

        return $this->redirect($url);
    }

    /**
     * @Template()
     */
    public function localeSwitcherAction()
    {
        // recuperation des langue
        /* @var $langue_provider Langue */
        $langue_provider = $this->get('cap_sciences_server_vip_navinum.providers.langue');
        $langues = $langue_provider->getAll();
        $langue_choices = array();

        foreach ($langues as $l) {
            /* @var $l \CapSciences\ServerVip\NavinumBundle\Model\Langue */
            $langue_choices[$l->getShortLibelle()] = $l->getLibelle();
        }

        return array('langue_choices' => $langue_choices);
    }



}
