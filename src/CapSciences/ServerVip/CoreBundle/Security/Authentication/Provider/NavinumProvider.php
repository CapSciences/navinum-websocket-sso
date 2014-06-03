<?php

namespace CapSciences\ServerVip\CoreBundle\Security\Authentication\Provider;

use CapSciences\ServerVip\CoreBundle\Security\User\NavinumUser;
use CapSciences\ServerVip\CoreBundle\Security\User\NavinumUserProvider;
use CapSciences\ServerVip\WebsocketBundle\Providers\Visiteur;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use CapSciences\ServerVip\CoreBundle\Security\Authentication\Token\FacebookUserToken;

class NavinumProvider implements AuthenticationProviderInterface
{
    private $userProvider;
    private $container;

    public function __construct(NavinumUserProvider $userProvider, ContainerInterface $container)
    {
        $this->userProvider = $userProvider;
        $this->container    = $container;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof UsernamePasswordToken || $token instanceof FacebookUserToken;
    }

    public function authenticate(TokenInterface $token)
    {
        if ($token instanceof UsernamePasswordToken) {
            $username = $token->getUsername();
            if (empty($username)) {
                $username = 'NONE_PROVIDED';
            }

            try {
                $user = $this->userProvider->findByUsernameAndPassword($username, $token->getCredentials());
            } catch (UsernameNotFoundException $ex) {
                throw new BadCredentialsException('Bad Credentials', 0, $ex);
            }

            $authenticatedToken = new UsernamePasswordToken($user, $token->getCredentials(), 'username', $user->getRoles());
            $authenticatedToken->setAttributes($token->getAttributes());

            return $authenticatedToken;
        } else {
            if ($token instanceof FacebookUserToken) {
                $user = $token->getUser();
                if (is_string($user) && !empty($user)) {
                    try {
                        $user = $this->userProvider->findByFacebookId($token->getUser());
                    } catch (UsernameNotFoundException $e) {
                        $this->createFacebookUser();
                        $user = $this->userProvider->findByFacebookId($token->getUser());
                    }
                }
                else {
                    $user = $this->createFacebookUser();
                }
                $authenticatedToken = new UsernamePasswordToken($user, $token->getCredentials(), 'username', $user->getRoles());
                $authenticatedToken->setAttributes($token->getAttributes());

                return $authenticatedToken;
            }
        }

        return null;
    }

    private function createFacebookUser()
    {
        $user_profile = $this->container->get('cap_sciences_server_vip_core.facebook')->api('/me');

        return $this->userProvider->createFacebookUser($user_profile);
    }
}