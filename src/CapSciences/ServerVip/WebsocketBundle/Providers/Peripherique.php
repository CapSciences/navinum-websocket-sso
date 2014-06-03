<?php
namespace CapSciences\ServerVip\WebsocketBundle\Providers;

class Peripherique extends ApiProvider
{
    const API_NAMESPACE = 'peripherique';

    public function find($id)
    {
        return $this->get(self::API_NAMESPACE . '/' . $id);
    }

    public function findOneByMacAddress($macAddress)
    {
        $result = $this->get(self::API_NAMESPACE . '?adresse_mac=' . $macAddress);
        if(is_array($result) && count($result) > 0) {
            return $result[0];
        }

        return null;
    }

}