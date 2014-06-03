<?php

namespace CapSciences\ServerVip\NavinumBundle\Model;

use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\AccessType;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Exclude;
use Symfony\Component\Validator\Constraints as Assert;

class Visiteur
{
    /**
     * @Type("string")
     */
    protected $guid;
    /**
     * @Accessor(getter="getContexteCreationId",setter="setContexteCreationId")
     * @Type("string")
     */
    protected $contexte_creation_id;
    /**
     * @Accessor(getter="getLangueId",setter="setLangueId")
     * @Type("string")
     */
    protected $langue_id;
    /**
     * @Type("string")
     */
    protected $adresse;
    /**
     * @Accessor(getter="getCodePostal",setter="setCodePostal")
     * @Type("string")
     */
    protected $code_postal;
    /**
     * @Accessor(getter="getDailymotionId",setter="setDailymotionId")
     * @Type("string")
     */
    protected $dailymotion_id;
    /**
     * @Accessor(getter="getDateNaissance",setter="setDateNaissance")
     * @Type("string")
     */
    protected $date_naissance;
    /**
     * @Accessor(getter="getContexteCreationId",setter="setContexteCreationId")
     * @Type("string")
     */
    protected $email;
    /**
     * @Accessor(getter="getContexteCreationId",setter="setContexteCreationId")
     * @Type("string")
     */
    protected $facebook_id;
    /**
     * @Accessor(getter="getContexteCreationId",setter="setContexteCreationId")
     * @Type("string")
     */
    protected $flickr_id;
    /**
     * @Accessor(getter="getContexteCreationId",setter="setContexteCreationId")
     * @Type("string")
     */
    protected $genre;
    /**
     * @Accessor(getter="getContexteCreationId",setter="setContexteCreationId")
     * @Type("string")
     */
    protected $google_id;
    /**
     * @Accessor(getter="getContexteCreationId",setter="setContexteCreationId")
     * @Type("string")
     */
    protected $has_photo;
    /**
     * @Accessor(getter="getContexteCreationId",setter="setContexteCreationId")
     * @Type("string")
     */
    protected $is_active;
    /**
     * @Accessor(getter="getContexteCreationId",setter="setContexteCreationId")
     * @Type("string")
     */
    protected $nom;
    /**
     * @Accessor(getter="getContexteCreationId",setter="setContexteCreationId")
     * @Type("string")
     */
    protected $num_mobile;
    /**
     * @Accessor(getter="getContexteCreationId",setter="setContexteCreationId")
     * @Type("string")
     */
    protected $password_son;
    /**
     * @Accessor(getter="getContexteCreationId",setter="setContexteCreationId")
     * @Type("string")
     */
    protected $prenom;
    /**
     * @Accessor(getter="getContexteCreationId",setter="setContexteCreationId")
     * @Type("string")
     */
    protected $pseudo_son;
    /**
     * @Accessor(getter="getContexteCreationId",setter="setContexteCreationId")
     * @Type("string")
     */
    protected $twitter_id;
    /**
     * @Accessor(getter="getContexteCreationId",setter="setContexteCreationId")
     * @Type("string")
     */
    protected $type;
    /**
     * @Accessor(getter="getContexteCreationId",setter="setContexteCreationId")
     * @Type("string")
     */
    protected $url_avatar;

    /**
     * @Accessor(getter="getContexteCreationId",setter="setContexteCreationId")
     * @Type("string")
     */
    protected $ville;

    /**
     * @Exclude
     */
    protected $has_newsletter;

    /**
     * @Exclude
     * @Assert\True
     *
     */
    protected $has_cgu;



    public function getAdresse()
    {
        return $this->adresse;
    }

    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    public function getCodePostal()
    {
        return $this->code_postal;
    }

    public function setCodePostal($code_postal)
    {
        $this->code_postal = $code_postal;
    }

    public function getContexteCreationId()
    {
        return $this->contexte_creation_id;
    }

    public function setContexteCreationId($contexte_creation_id)
    {
        $this->contexte_creation_id = $contexte_creation_id;
    }

    public function getDailymotionId()
    {
        return $this->dailymotion_id;
    }

    public function setDailymotionId($dailymotion_id)
    {
        $this->dailymotion_id = $dailymotion_id;
    }

    public function getDateNaissance()
    {
        return $this->date_naissance;
    }

    public function setDateNaissance($date_naissance)
    {
        $this->date_naissance = $date_naissance;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    public function setFacebookId($facebook_id)
    {
        $this->facebook_id = $facebook_id;
    }

    public function getFlickrId()
    {
        return $this->flickr_id;
    }

    public function setFlickrId($flickr_id)
    {
        $this->flickr_id = $flickr_id;
    }

    public function getGenre()
    {
        return $this->genre;
    }

    public function setGenre($genre)
    {
        $this->genre = $genre;
    }

    public function getGoogleId()
    {
        return $this->google_id;
    }

    public function setGoogleId($google_id)
    {
        $this->google_id = $google_id;
    }

    public function getGuid()
    {
        return $this->guid;
    }

    public function setGuid($guid)
    {
        $this->guid = $guid;
    }

    public function getHasPhoto()
    {
        return $this->has_photo;
    }

    public function setHasPhoto($has_photo)
    {
        $this->has_photo = $has_photo;
    }

    public function getIsActive()
    {
        return $this->is_active;
    }

    public function setIsActive($is_active)
    {
        $this->is_active = $is_active;
    }

    public function getLangueId()
    {
        return $this->langue_id;
    }

    public function setLangueId($langue_id)
    {
        $this->langue_id = $langue_id;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    public function getNumMobile()
    {
        return $this->num_mobile;
    }

    public function setNumMobile($num_mobile)
    {
        $this->num_mobile = $num_mobile;
    }

    public function getPasswordSon()
    {
        return $this->password_son;
    }

    public function setPasswordSon($password_son)
    {
        $this->password_son = $password_son;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }

    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    public function getPseudoSon()
    {
        return $this->pseudo_son;
    }

    public function setPseudoSon($pseudo_son)
    {
        $this->pseudo_son = $pseudo_son;
    }

    public function getTwitterId()
    {
        return $this->twitter_id;
    }

    public function setTwitterId($twitter_id)
    {
        $this->twitter_id = $twitter_id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getUrlAvatar()
    {
        return $this->url_avatar;
    }

    public function setUrlAvatar($url_avatar)
    {
        $this->url_avatar = $url_avatar;
    }

    public function getVille()
    {
        return $this->ville;
    }

    public function setVille($ville)
    {
        $this->ville = $ville;
    }

    public function setHasNewsletter($has_newsletter)
    {
        $this->has_newsletter = $has_newsletter;
    }

    public function getHasNewsletter()
    {
        return $this->has_newsletter;
    }

    public function setHasCgu($has_cgu)
    {
        $this->has_cgu = $has_cgu;
    }

    public function getHasCgu()
    {
        return $this->has_cgu;
    }


}