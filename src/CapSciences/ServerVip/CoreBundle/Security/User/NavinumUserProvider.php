<?php

namespace CapSciences\ServerVip\CoreBundle\Security\User;

use CapSciences\ServerVip\CoreBundle\Security\User\NavinumUser;
use CapSciences\ServerVip\NavinumBundle\Providers\Visiteur;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class NavinumUserProvider implements UserProviderInterface
{
    private $visiteurProvider;

    public function __construct(Visiteur $visiteurProvider)
    {
        $this->visiteurProvider = $visiteurProvider;
    }

    public function loadUserByUsername($username)
    {
        throw new \BadMethodCallException('loadByUsername not available for navinum');
    }

    public function findByFacebookId($facebookId)
    {
        $userData = $this->visiteurProvider->findByFacebookId($facebookId);

        if (!$userData || (is_array($userData) && count($userData) == 0)) {
            throw new UsernameNotFoundException('User does not exist anymore.');
        }

        return new NavinumUser($userData[0]);
    }

    /**
     * @param $user
     */
    public function createFacebookUser($user_profile)
    {

        $visiteur_facebook = new \CapSciences\ServerVip\NavinumBundle\Model\Visiteur();
        $visiteur_facebook->setEmail($user_profile['email']);
        $visiteur_facebook->setPseudoSon($user_profile['username']);
        $visiteur_facebook->setNom($user_profile['last_name']);
        $visiteur_facebook->setPrenom($user_profile['first_name']);
        $visiteur_facebook->setGenre($user_profile['gender'] == 'male' ? 'H' : 'F');
        $visiteur_facebook->setFacebookId($user_profile['id']);
        if ($this->visiteurProvider->create($visiteur_facebook, true)) {
            return new NavinumUser($visiteur_facebook);
        }

        throw new \Exception('An error occured when creating visitor from facebook.' . PHP_EOL . print_r($visiteur_facebook, true));
    }

    public
    function refreshUser(
        UserInterface $user
    )
    {
        if (!$user instanceof NavinumUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->findByUsernameAndPassword($user->getUsername(), $user->getPassword(), true);
    }

    public function findByUsernameAndPassword($username, $password, $encoded = false)
    {
        $userData = $this->visiteurProvider->findByUsernameAndPassword($username, $password, $encoded);

        if (!$userData || (is_array($userData) && count($userData) == 0)) {
            throw new UsernameNotFoundException('User does not exist anymore.');
        }

        return new NavinumUser($userData[0]);
    }

    public
    function supportsClass(
        $class
    )
    {
        return $class === 'CapSciences\ServerVip\CoreBundle\Security\User\NavinumUser';
    }

    public function update($user)
    {
        return $this->visiteurProvider->update($user);
    }
}
