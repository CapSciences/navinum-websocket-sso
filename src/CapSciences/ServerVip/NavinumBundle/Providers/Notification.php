<?php
namespace CapSciences\ServerVip\NavinumBundle\Providers;

class Notification extends ApiProvider
{
    const API_NAMESPACE = 'notification';

    public function find($id)
    {
        return $this->request(self::API_NAMESPACE . '/' . $id);
    }

    public function create($data)
    {
        $result = $this->post(self::API_NAMESPACE, $data);
        if(is_array($result) && count($result) == 1) {
            return $result[0];
        }

        return null;
    }

    public function getAllByUser($id=null){
        if($id !=null){
            return $this->get(self::API_NAMESPACE."?visiteur_id=".$id);
        }
        return $this->get(self::API_NAMESPACE);
    }
}
