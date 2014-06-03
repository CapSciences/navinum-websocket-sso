<?php
namespace CapSciences\ServerVip\WebsocketBundle\Providers;

class Flotte extends ApiProvider
{
    const API_NAMESPACE = 'flotte';

    public function find($id)
    {
        return $this->get(self::API_NAMESPACE . '/' . $id);
    }

}