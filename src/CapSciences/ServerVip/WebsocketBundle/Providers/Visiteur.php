<?php
namespace CapSciences\ServerVip\WebsocketBundle\Providers;

class Visiteur extends ApiProvider
{
    const API_NAMESPACE = 'visiteur';

    public function find($id)
    {
        return $this->get(self::API_NAMESPACE . '/' . $id);
    }

    public function findByUsernameAndPassword($username, $password, $allreadyEncoded = false)
    {
        if (!$allreadyEncoded) {
            $password = md5($password);
        }

        return $this->get(sprintf('%s?pseudo_son=%s&password_son=%s', self::API_NAMESPACE, $username, $password));
    }
}
