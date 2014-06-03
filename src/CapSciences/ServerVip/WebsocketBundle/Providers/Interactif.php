<?php
namespace CapSciences\ServerVip\WebsocketBundle\Providers;

class Interactif extends ApiProvider
{
    const API_NAMESPACE = 'interactif';

    public function find($id)
    {
        return $this->get(self::API_NAMESPACE . '/' . $id);
    }
}
