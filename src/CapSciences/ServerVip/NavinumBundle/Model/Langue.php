<?php
/**
 * User: tperrin
 * Date: 19/04/13
 * Time: 12:09
 */

namespace CapSciences\ServerVip\NavinumBundle\Model;

use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\AccessType;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Expose;

/**
 * Class Langue
 * @package CapSciences\ServerVip\NavinumBundle\Model
 */
class Langue
{
    /**
     * @Type("string")
     */
    protected $guid;
    /**
     * @Type("string")
     */
    protected $libelle;
    /**
     * @Accessor(getter="getShortLibelle",setter="setShortLibelle")
     * @Type("string")
     */
    protected $short_libelle;

    public function setGuid($guid)
    {
        $this->guid = $guid;
    }

    public function getGuid()
    {
        return $this->guid;
    }

    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    }

    public function getLibelle()
    {
        return $this->libelle;
    }

    public function setShortLibelle($short_libelle)
    {
        $this->short_libelle = $short_libelle;
    }

    public function getShortLibelle()
    {
        return $this->short_libelle;
    }
}