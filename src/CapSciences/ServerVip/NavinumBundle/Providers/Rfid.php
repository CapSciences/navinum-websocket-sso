<?php
namespace CapSciences\ServerVip\NavinumBundle\Providers;

class Rfid extends ApiProvider
{
    const API_NAMESPACE = 'rfid';

    public function find($id)
    {
        return $this->get(self::API_NAMESPACE . '/' . $id);
    }
}
