<?php

namespace CapSciences\ServerVip\CoreBundle\Controller;

use CapSciences\ServerVip\CoreBundle\Security\User\NavinumUser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;

class SsoController extends Controller
{
    /**
     * @Route("/sso/whoami")
     * @Template()
     */
    public function whoamiAction()
    {
        /** @var $user NavinumUser */
        $user = $this->getUser();

        return new Response($user->asJson(), 200, array('Content-type' => 'application/json'));
    }
}
