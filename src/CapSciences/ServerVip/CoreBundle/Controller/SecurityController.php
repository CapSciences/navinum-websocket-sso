<?php

namespace CapSciences\ServerVip\CoreBundle\Controller;

use CapSciences\ServerVip\CoreBundle\Form\UserType;
use CapSciences\ServerVip\NavinumBundle\Providers\Message;
use CapSciences\ServerVip\NavinumBundle\Providers\Visiteur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SecurityController extends Controller
{
    /**
     * @Route("/user/security/login")
     * @Template()
     */
    public function loginAction()
    {
        if ( $this->get( 'security.context' )->isGranted( 'IS_AUTHENTICATED_FULLY' ) )
        {
            $referer = $this->getRequest()->headers->get( 'Referer' );
            if ( $referer )
            {
                return $this->redirect( $this->getRequest()->headers->get( 'Referer' ) );
            }

            if ( $this->getRequest()->getSession()->has( '_security.oauth_authorize.target_path' ) )
            {
                return $this->redirect(
                    $this->getRequest()->getSession()->get( '_security.oauth_authorize.target_path' )
                );
            }
        }

        $request = $this->getRequest();
        $session = $request->getSession();

        $formSignup = $this->createVisiteurSignupForm();

        // get the login error if there is one
        if ( $request->attributes->has( SecurityContext::AUTHENTICATION_ERROR ) )
        {
            $error = $request->attributes->get( SecurityContext::AUTHENTICATION_ERROR );
        }
        else
        {
            $error = $session->get( SecurityContext::AUTHENTICATION_ERROR );
        }

        $config = array(
            'appId' => $this->container->getParameter( 'api_facebook_id' ),
            'secret' => $this->container->getParameter( 'api_facebook_secret' ),
        );

        $facebook = new \Facebook( $config );

        $redirect_uri = $request->get( 'redirect_uri', $this->container->getParameter('default_redirect_uri') );

        $url = $facebook->getLoginUrl(
            array(
                'scope' => $this->container->getParameter( 'api_facebook_scope' ),
                'redirect_uri' => $this->generateUrl(
                    'capsciences_servervip_core_security_loginfacebook',
                    array(
                        'redirect_uri' => $redirect_uri
                    ),
                    true
                )
            )
        );

        return array(
            // last username entered by the user
            'last_username' => $session->get( SecurityContext::LAST_USERNAME ),
            'error' => $error != null ? $error->getMessage() : null,
            'form' => $formSignup->createView(),
            'facebook_login' => $url
        );
    }

    protected function createVisiteurSignupForm( $visiteur = null )
    {
        return $this->createForm( new UserType(), $visiteur );
    }

    /**
     * @Route("/facebook")
     */
    public function loginFacebookAction()
    {
        return $this->redirect(
            'capsciences_servervip_core_user_update',
            array(),
            true
        );
    }

    /**
     * @Route("/user/security/logout")
     */
    public function logoutAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // Log the user out
        $session->invalidate();
        $this->get( "security.context" )->setToken( null );

//        $facebook

        $facebook->destroySession();
        $facebook->setAccessToken( null );

        $redirect_uri = $request->get( 'redirect_uri', 'http://www.c-yourmag.net');

        return $this->redirect( $redirect_uri );
    }

    /**
     * @Route("/retrieve/{info}")
     * @Template()
     */
    public function retrieveAction( $info )
    {
        if ( $info == "login" || $info == "password" )
        {
            $name = $info;
        }
        else
        {
            $name = "";
        }

        return array( 'name' => $name );
    }

    /**
     * @Route("/security/lost-login/{method}")
     * @Template()
     */
    public function lostLoginAction( $method )
    {
        $form = null;
        $success = false;
        if ( $method == 'sms' )
        {
            $request = $this->getRequest();
            $defaultData = array( 'email' => '', 'mobile' => '' );
            $form = $this->createFormBuilder( $defaultData )
                ->add( 'mobile', 'text' )
                ->add( 'email', 'email' )
                ->getForm();

            if ( $request->isMethod( 'POST' ) )
            {
                $form->bind( $request );

                // data is an array with "name", "email", and "message" keys
                $data = $form->getData();

                /* @var $provider Message */
                $provider = $this->get( 'cap_sciences_server_vip_navinum.providers.message' );
                try
                {
                    $result = $provider->sendPseudoSms( $data['email'], $data['mobile'] );
                    $success = $data['mobile'];
                }
                catch ( \Exception $e )
                {
                    $response = json_decode( $e->getMessage() );
                    $message = $response[0]->message;
                    $form->addError( new FormError( $message ) );
                }
            }
        }
        else
        {
            $request = $this->getRequest();
            $defaultData = array( 'email' => '', 'mobile' => '' );
            $form = $this->createFormBuilder( $defaultData )
                ->add( 'email', 'email' )
                ->getForm();

            if ( $request->isMethod( 'POST' ) )
            {
                $form->bind( $request );

                // data is an array with "name", "email", and "message" keys
                $data = $form->getData();
                /* @var $provider Message */
                $provider = $this->get( 'cap_sciences_server_vip_navinum.providers.message' );
                try
                {
                    $result = $provider->sendPseudo( $data['email'] );
                    $success = $data['email'];
                }
                catch ( \Exception $e )
                {
                    $response = json_decode( $e->getMessage() );
                    $message = $response[0]->message;
                    $form->addError( new FormError( $message ) );
                }
            }
        }

        return array( 'form' => $form->createView(), 'method' => $method, 'success' => $success );

    }

    /**
     * @Route("/security/lost-password/{method}")
     * @Template()
     */
    public function lostPasswordAction( $method )
    {
        $form = null;
        $success = false;
        if ( $method == 'sms' )
        {
            $request = $this->getRequest();
            $defaultData = array( 'login' => '', 'mobile' => '' );
            $form = $this->createFormBuilder( $defaultData )
                ->add( 'login', 'text' )
                ->add( 'mobile', 'text' )
                ->getForm();

            if ( $request->isMethod( 'POST' ) )
            {
                $form->bind( $request );

                $data = $form->getData();
                /* @var $provider message */
                $provider = $this->get( 'cap_sciences_server_vip_navinum.providers.message' );
                try
                {
                    $result = $provider->resetUserPasswordSms( $data['login'], $data['mobile'] );
                    $success = $data['mobile'];
                }
                catch ( \Exception $e )
                {
                    $response = json_decode( $e->getMessage() );
                    $message = $response[0]->message;
                    $form->addError( new FormError( $message ) );
                }
            }

        }
        else
        {
            $request = $this->getRequest();
            $defaultData = array( 'login' => '', 'email' => '' );
            $form = $this->createFormBuilder( $defaultData )
                ->add( 'login', 'text' )
                ->add( 'email', 'email' )
                ->getForm();

            if ( $request->isMethod( 'POST' ) )
            {
                $form->bind( $request );

                $data = $form->getData();
                /* @var $provider Message */
                $provider = $this->get( 'cap_sciences_server_vip_navinum.providers.message' );
                try
                {
                    $result = $provider->resetUserPassword( $data['login'], $data['email'] );
                    $success = $data['email'];
                }
                catch ( \Exception $e )
                {
                    $response = json_decode( $e->getMessage() );
                    $message = $response[0]->message;
                    $form->addError( new FormError( $message ) );
                }
            }
        }

        return array( 'form' => $form->createView(), 'method' => $method, 'success' => $success );
    }

    /**
     * @Route("/security/activate/{guid}")
     * @Template()
     */
    public function activateAction( $guid )
    {
        /* @var $provider Visiteur */
        $provider = $this->get( 'cap_sciences_server_vip_navinum.providers.visiteur' );
        $result = $provider->activate( $guid );

        return array();
    }

    /**
     * @Route("/security/signup")
     * @Template()
     */
    public function signupAction()
    {

        /* @var $request Request */
        $request = $this->getRequest();
        $visiteur = new \CapSciences\ServerVip\NavinumBundle\Model\Visiteur();
        $form = $this->createVisiteurSignupForm( $visiteur );

        if ( $request->isMethod( "POST" ) )
        {
            $form->bind( $request );

            if ( $form->isValid() && $this->get( 'cap_sciences_server_vip_core.form.handler.usertype' )->handle(
                    $form,
                    $visiteur
                )
            )
            {
                // OK
                return array();
            }
        }

        return $this->render(
            'CapSciencesServerVipCoreBundle:Security:login.html.twig',
            array( 'form' => $form->createView() )
        );
    }
}
