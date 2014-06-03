<?php

namespace CapSciences\ServerVip\CoreBundle\Controller;

use CapSciences\ServerVip\CoreBundle\Security\User\NavinumUser;
use CapSciences\ServerVip\NavinumBundle\Providers\Visiteur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @Route("/user")
     * @Route("/user/")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/user/update")
     * @Template()
     */
    public function updateAction()
    {
        $user = new NavinumUser( $this->getUser()->getNavinumData() );

        $request = $this->getRequest();
        $redirect_uri = $request->get( 'redirect_uri', $this->container->getParameter( 'default_redirect_uri' ) );

        // recuperation des langue
        /* @var $langue_provider Langue */
        $langue_provider = $this->get( 'cap_sciences_server_vip_navinum.providers.langue' );
        $langues = $langue_provider->getAll();
        $langue_choices = array();

        $messages = array();

        foreach ($langues as $l) {
            /* @var $l \CapSciences\ServerVip\NavinumBundle\Model\Langue */
            $langue_choices[$l->getGuid()] = $l->getLibelle();
        }


        $form = $this->createFormBuilder( $user )
            ->add( 'guid', 'hidden' )
            ->add(
                'genre',
                'choice',
                array(
                    'choices' => array('H' => 'Homme', 'F' => 'Femme'),
                    'label' => 'Genre'
                )
            )
            ->add( 'prenom', 'text', array('required' => false, 'label' => 'Prénom') )
            ->add( 'pseudo_son', 'text', array('label' => 'Pseudo') )
            ->add( 'nom', 'text', array('required' => false) )
            ->add( 'date_naissance', 'birthday', array('format' => 'dd-MM-yyyy') )
            ->add( 'email', 'email' )
            ->add( 'num_mobile', 'text' )
            ->add( 'adresse', 'text', array('required' => false) )
            ->add( 'code_postal', 'text' )
            ->add( 'ville', 'text' )
            ->add( 'langue_id', 'choice', array('choices' => $langue_choices, 'label' => 'Langue') )
            ->add( 'has_newsletter', 'checkbox', array('label' => 'Newsletter', 'required' => false) )
            ->add( 'type', 'hidden' )
            ->getForm();

        if ($request->isMethod( 'POST' )) {
            $form->bind( $request );
            $redirect_uri = $request->get( 'redirect_uri', $this->container->getParameter( 'default_redirect_uri' ) );

            // data is an array with "name", "email", and "message" keys
            $data = $form->getData();

            /* @var $user_provider NavinumUserProvider */
            $user_provider = $this->get( 'navinum_user_provider' );
            $wsreturn = $user_provider->update( $data );
            $translator = $this->get( 'translator' );

            $messages[] = $translator->trans( "Votre profil a été mis à jour." );

            return $this->redirect( $redirect_uri );
        }

        return array('form' => $form->createView(), 'messages' => $messages, 'redirect_uri' => $redirect_uri);
    }

    /**
     * @Route("/user/delete")
     * @Template()
     */
    public function deleteAction()
    {
        $user = new NavinumUser( $this->getUser()->getNavinumData() );
        /* @var $visiteur_provider Visiteur */
        $visiteur_provider = $this->get( 'cap_sciences_server_vip_navinum.providers.visiteur' );

        $request = $this->getRequest();
        $redirect_uri = $request->get( 'redirect_uri', $this->container->getParameter( 'default_redirect_uri' ) );

        try {
            $response = $visiteur_provider->delete( $user->getGuid() );
            $this->getRequest()->getSession()->clear();

            return $this->redirect( $redirect_uri );
        } catch (\Exception $e) {
            $response = json_decode( $e->getMessage() );
            $message = $response[0]->message;
            $errors[] = $message;

            return array('errors' => $errors);
        }

        return array();
    }

    private function parseNotifParameters( $parameters )
    {
        // on supprime les caractere inutile
        $msg = preg_replace( '#(O:8:"stdClass":\d*:{)|(s:\d*:)|}#', '', $parameters );
        // on supprime le dernier point virgule
        $msg = substr( $msg, 0, -1 );
        // on explode en array
        $arr = explode( ';', $msg );

        $title = str_replace( '"', '', $arr[1] );
        $msg = str_replace( '"', '', $arr[3] );

        return array(
            'title' => $title,
            'message' => $msg
        );
    }

    /**
     * @Route("user/timeline")
     * @Template
     */
    public function timelineAction()
    {
        /* @var $provider \CapSciences\ServerVip\NavinumBundle\Providers\Notification */
        $provider = $visiteur_provider = $this->get( 'cap_sciences_server_vip_navinum.providers.notification' );
        $user = $this->getUser()->getNavinumData();
//        $notifs = $provider->getAllByUser();
        $notifs = $provider->getAllByUser($user->guid);

        foreach ($notifs as $notif) {
            $notif->parameter = $this->parseNotifParameters( $notif->parameter );
            $now = new \DateTime();
            $update = new \DateTime($notif->updated_at);
            $diff = $now->diff($update);

            if($diff->d != 0){
                if($diff->h > 1)
                    $notif->time = $diff->format('%d jours %h heures %m min');
                else
                    $notif->time = $diff->format('%d jours %h heure %m min');
            }else if($diff->h != 0){
                if($diff->h > 1)
                    $notif->time = $diff->format('%h heures %m min');
                else
                    $notif->time = $diff->format('%h heure %m min');
            }else if($diff->m != 0){
                $notif->time = $diff->format('%m min');
            }


        }

        return array('notifications' => $notifs);
    }
    /**
     * @Route("user/sendPhoto")
     */
    public function sendPhoto( ){
        $guid = $this->getRequest()->request->get('guid');
        $photo = $this->getRequest()->request->get('photo');

        /* @var $provider Visiteur */
        $provider = $this->get( 'cap_sciences_server_vip_navinum.providers.visiteur' );
        $result = $provider->sendPhoto( $guid, $photo );
        return new Response();
    }
}
