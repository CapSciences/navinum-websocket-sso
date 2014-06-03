<?php

namespace CapSciences\ServerVip\CoreBundle\Controller;

use CapSciences\ServerVip\CoreBundle\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AdminController extends Controller
{
    /**
     * @Route("/admin")
     * @Template()
     */
    public function indexAction(){
        return array();
    }

    /**
     * @Route("/admin/sso/auth")
     * @Template()
     */
    public function listAppAction()
    {
        $apps = $this->getDoctrine()
            ->getRepository('CapSciencesServerVipCoreBundle:Client')
            ->findAll();

        return array('apps' => $apps);
    }

    /**
     * @Route("/admin/sso/auth/add")
     * @Template()
     */
    public function addAppAction()
    {
        $client = new Client();
        $form = $this->createFormBuilder($client)
            ->add('name', 'text', array('label' => 'Nom'))
            ->add('uri', 'url', array('label' => 'Hôte'))
            ->getForm();

        $request = $this->getRequest();

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                // data is an array with "name", "email", and "message" keys
                $data = $form->getData();
                $clientManager = $this->get('fos_oauth_server.client_manager.default');
                /* @var $client \CapSciences\ServerVip\CoreBundle\Entity\Client */
                $client = $clientManager->createClient();
                $client->setName($data->getName());
                $client->setRedirectUris(array($data->getUri()));
                $client->setAllowedGrantTypes(array('token', 'authorization_code'));
                $clientManager->updateClient($client);

                return $this->redirect($this->generateUrl('capsciences_servervip_core_admin_listapp'));
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/admin/sso/auth/delete/{id}")
     */
    public function delAppAction($id)
    {
        /* @var $tokenManager \FOS\OAuthServerBundle\Entity\AccessTokenManager*/
        $tokenManager = $this->get('fos_oauth_server.access_token_manager.default');
        /* @var $authManager \FOS\OAuthServerBundle\Entity\AuthCodeManager*/
        $authManager = $this->get('fos_oauth_server.auth_code_manager.default');
        /* @var $refreshManager \FOS\OAuthServerBundle\Entity\RefreshTokenManager*/
        $refreshManager = $this->get('fos_oauth_server.refresh_token_manager.default');
        /* @var $clientManager \FOS\OAuthServerBundle\Entity\ClientManager*/
        $clientManager = $this->get('fos_oauth_server.client_manager.default');

        //recuperation du client
        $client = $this->getDoctrine()
            ->getRepository('CapSciencesServerVipCoreBundle:Client')
            ->find($id);
        $auth = $this->getDoctrine()
            ->getRepository('CapSciencesServerVipCoreBundle:AuthCode')
            ->findBy(array('client'=>$client));
        $token = $this->getDoctrine()
            ->getRepository('CapSciencesServerVipCoreBundle:AccessToken')
            ->findBy(array('client'=>$client));
        $refresh = $this->getDoctrine()
            ->getRepository('CapSciencesServerVipCoreBundle:RefreshToken')
            ->findBy(array('client'=>$client));
        //suppression
        foreach($auth as $a){
            $authManager->deleteAuthCode($a);
        }
        foreach($token as $t){
            $tokenManager->deleteToken($t);
        }
        foreach($refresh as $t){
            $refreshManager->deleteToken($t);
        }
        $clientManager->deleteClient($client);

        //redirection liste des des authorization
        return $this->redirect($this->generateUrl('capsciences_servervip_core_admin_listapp'));
    }
}
