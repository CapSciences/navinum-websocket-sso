<?php
namespace CapSciences\ServerVip\CoreBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class FacebookUserToken extends AbstractToken
{
    public function __construct($user, array $roles = array())
    {
        parent::__construct($roles);
        $this->setUser($user);
    }

    public function getCredentials()
    {
        return '';
    }
}