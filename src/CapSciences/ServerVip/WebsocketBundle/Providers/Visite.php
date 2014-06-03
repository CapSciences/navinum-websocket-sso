<?php
namespace CapSciences\ServerVip\WebsocketBundle\Providers;

class Visite extends ApiProvider
{
    const API_NAMESPACE = 'visite';

    public function find($id)
    {
        return $this->get(self::API_NAMESPACE . '/' . $id);
    }

    public function findOneByVisiteur($visiteurId)
    {
        $result = $this->get(self::API_NAMESPACE.'?visiteur_id='.$visiteurId);
        if(is_array($result) && count($result) > 0) {
            return $result[0];
        }

        return null;
    }
}
