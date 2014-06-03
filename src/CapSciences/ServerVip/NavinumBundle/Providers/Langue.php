<?php
/**
 * User: tperrin
 * Date: 19/04/13
 * Time: 12:09
 */

namespace CapSciences\ServerVip\NavinumBundle\Providers;


class Langue extends ApiProvider{

    const API_NAMESPACE = 'langue';

    public function getAll(){
        return $this->get(self::API_NAMESPACE, sprintf('array<%s>',$this->getModelClass()));
    }

    protected function getModelClass()
    {
        return 'CapSciences\ServerVip\NavinumBundle\Model\Langue';
    }
}