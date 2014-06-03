<?php

namespace CapSciences\ServerVip\CoreBundle\Security\Http\Firewall;

use CapSciences\ServerVip\CoreBundle\Security\Authentication\Token\FacebookUserToken;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\SessionUnavailableException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;

/**
 * NavinumAuthenticationListener
 */
class NavinumAuthenticationListener implements ListenerInterface
{
    private $securityContext;
    private $sessionStrategy;
    private $dispatcher;
    private $container;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        SessionAuthenticationStrategyInterface $sessionStrategy,
        HttpUtils $httpUtils,
        $providerKey,
        array $options = array(),
        LoggerInterface $logger = null,
        EventDispatcherInterface $dispatcher = null,
        ContainerInterface $container
    )
    {
        if (empty($providerKey)) {
            throw new \InvalidArgumentException('$providerKey must not be empty.');
        }

        $this->securityContext       = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->sessionStrategy       = $sessionStrategy;
        $this->providerKey           = $providerKey;
        $this->options               = array_merge(
            array(
                'intention' => 'authenticate'
            ),
            $options
        );
        $this->logger                = $logger;
        $this->dispatcher            = $dispatcher;
        $this->httpUtils             = $httpUtils;
        $this->container             = $container;
    }

    /**
     * This interface must be implemented by firewall listeners.
     *
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$this->requiresAuthentication($request)) {
            return;
        }

        if (!$request->hasSession()) {
            throw new \RuntimeException('This authentication method requires a session.');
        }

        try {
            if (!$request->hasPreviousSession()) {
//                throw new SessionUnavailableException( 'Your session has timed out, or you have disabled cookies.' );
            }

            if (null === $returnValue = $this->attemptAuthentication($request)) {
                return;
            }

            if ($returnValue instanceof TokenInterface) {
                $this->sessionStrategy->onAuthentication($request, $returnValue);

                $response = $this->onSuccess($event, $request, $returnValue);
            } elseif ($returnValue instanceof Response) {
                $response = $returnValue;
            } else {
                throw new \RuntimeException('attemptAuthentication() must either return a Response, an implementation of TokenInterface, or null.');
            }
        } catch (AuthenticationException $e) {
            $response = $this->onFailure($event, $request, $e);
        }

        $event->setResponse($response);
    }

    /**
     * {@inheritdoc}
     */
    protected function requiresAuthentication(Request $request)
    {
        if ($request->isMethod('POST')) {
            return $this->httpUtils->checkRequestPath($request, $this->options['check_path']);
        }

        if ($request->isMethod('GET')) {
            return $this->httpUtils->checkRequestPath($request, "/facebook");
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        if ($request->isMethod('POST')) {
            // Login classique
            $username = $request->request->has('_username') ? $request->request->get('_username') : null;
            $password = $request->request->has('_password') ? $request->request->get('_password') : null;

            $username = empty($username) ? '_NOT_PROVIDED_' : $username;

            return $this->authenticationManager->authenticate(
                new UsernamePasswordToken($username, $password, $this->providerKey)
            );
        }

        if ($request->isMethod('GET')) {
            // Auth facebook
            return $this->attemptAuthenticationFacebook($request);
        }

        return null;
    }

    private function attemptAuthenticationFacebook(Request $request)
    {
        if($request->query->has('error_code')) {
            throw new \Exception($request->query->get('error_message'), $request->query->get('error_code'));
        }

        $user = $this->container->get('cap_sciences_server_vip_core.facebook')->getUser();

        return $this->authenticationManager->authenticate(
            new FacebookUserToken($user == 0 ? '' : $user)
        );
    }

    private function onSuccess(GetResponseEvent $event, Request $request, TokenInterface $token)
    {
        if (null !== $this->logger) {
            $this->logger->info(sprintf('User "%s" has been authenticated successfully', $token->getUsername()));
        }

        $this->securityContext->setToken($token);

        $session = $request->getSession();
        $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);

        $targetUrl = $this->determineTargetUrl($request);

        return $this->httpUtils->createRedirectResponse($request, $targetUrl);
    }

    protected function determineTargetUrl(Request $request)
    {

        if (null !== $this->providerKey && $targetUrl = $request->getSession()->get(
                '_security.' . $this->providerKey . '.target_path'
            )
        ) {
            $request->getSession()->remove('_security.' . $this->providerKey . '.target_path');

            return $targetUrl;
        }

        if ($targetUrl = $request->headers->get('Referer') && $targetUrl !== $this->httpUtils->generateUri(
                $request,
                $this->options['login_path']
            )
            && is_string($targetUrl)
        ) {
            return $targetUrl;
        }

        return '/user/update';

        throw new HttpException('Bad usage', 400);
    }

    private function onFailure(GetResponseEvent $event, Request $request, AuthenticationException $failed)
    {
        if (null !== $this->logger) {
            $this->logger->info(sprintf('Authentication request failed: %s', $failed->getMessage()));
        }

        $this->securityContext->setToken(null);
        $request->getSession()->set(SecurityContextInterface::AUTHENTICATION_ERROR, $failed);

        return $this->httpUtils->createRedirectResponse($request, $this->options['login_path']);
    }
}
