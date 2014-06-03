<?php
namespace CapSciences\ServerVip\NavinumBundle\Providers;

class Interactif extends ApiProvider
{
    const API_NAMESPACE = 'interactif';

    /**
     * @param $id
     *
     * @return \CapSciences\ServerVip\NavinumBundle\Model\Interactif
     */
    public function find($id)
    {
        return $this->get(self::API_NAMESPACE . '/' . $id);
    }

    public function findBy(array $criterias)
    {

        $flatCriterias = array();
        foreach ($criterias as $param => $value) {
            $flatCriterias[] = $param . '=' . urlencode($value);
        }

        return $this->get(self::API_NAMESPACE . '?' . implode('&', $flatCriterias), sprintf('array<%s>',$this->getModelClass()));
    }

    protected function getModelClass()
    {
        return 'CapSciences\ServerVip\NavinumBundle\Model\Interactif';
    }


}
