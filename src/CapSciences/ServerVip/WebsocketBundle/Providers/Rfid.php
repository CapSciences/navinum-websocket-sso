<?php
namespace CapSciences\ServerVip\WebsocketBundle\Providers;

class Rfid extends ApiProvider
{
    const API_NAMESPACE = 'rfid';

    public function find($id)
    {
        return $this->get(self::API_NAMESPACE . '/' . $id);
    }
}
