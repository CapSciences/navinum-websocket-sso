<?php

namespace CapSciences\ServerVip\CoreBundle\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;

class NavinumUser implements UserInterface
{
    private $navinumData;

    public function __construct($navinumData)
    {
        $this->navinumData = $navinumData;
    }

    public function setNavinumData($navinumData)
    {
        $this->navinumData = $navinumData;
    }

    public function getNavinumData()
    {
        return $this->navinumData;
    }

    public function asJson()
    {
        return json_encode($this->navinumData);
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getPassword()
    {
        return $this->navinumData->password_son;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->navinumData->pseudo_son;
    }

    public function eraseCredentials()
    {
    }

    public function getGuid()
    {
        return $this->navinumData->guid;
    }

    public function setGuid($guid)
    {
        $this->navinumData->guid = $guid;
    }

    public function getAdresse()
    {
        return $this->navinumData->adresse;
    }

    public function setAdresse($adresse)
    {
        $this->navinumData->adresse = $adresse;
    }

    public function getCodePostal()
    {
        return $this->navinumData->code_postal;
    }

    public function setCodePostal($code_postal)
    {
        $this->navinumData->code_postal = $code_postal;
    }

    public function getContexteCreationId()
    {
        return $this->navinumData->contexte_creation_id;
    }

    public function setContexteCreationId($contexte_creation_id)
    {
        $this->navinumData->contexte_creation_id = $contexte_creation_id;
    }

    public function getDailymotionId()
    {
        return $this->navinumData->dailymotion_id;
    }

    public function setDailymotionId($dailymotion_id)
    {
        $this->navinumData->dailymotion_id = $dailymotion_id;
    }

    public function getDateNaissance()
    {
        return new \DateTime($this->navinumData->date_naissance);
    }

    public function setDateNaissance($date_naissance)
    {
        $this->navinumData->date_naissance = $date_naissance;
    }

    public function getHasNewsletter(){
        return $this->navinumData->has_newsletter;
    }

    public function setHasNewsletter($has_newsletter){
        $this->navinumData->has_newsletter = $has_newsletter;
    }

    public function getEmail()
    {
        return $this->navinumData->email;
    }

    public function setEmail($email)
    {
        $this->navinumData->email = $email;
    }

    public function getFacebookId()
    {
        return $this->navinumData->facebook_id;
    }

    public function setFacebookId($facebook_id)
    {
        $this->navinumData->facebook_id = $facebook_id;
    }

    public function getFlickrId()
    {
        return $this->navinumData->flickr_id;
    }

    public function setFlickrId($flickr_id)
    {
        $this->navinumData->flickr_id = $flickr_id;
    }

    public function getGenre()
    {
        return $this->navinumData->genre;
    }

    public function setGenre($genre)
    {
        $this->navinumData->genre = $genre;
    }

    public function getGoogleId()
    {
        return $this->navinumData->google_id;
    }

    public function setGoogleId($google_id)
    {
        $this->navinumData->google_id = $google_id;
    }

    public function getHasPhoto()
    {
        return $this->navinumData->has_photo;
    }

    public function setHasPhoto($has_photo)
    {
        $this->navinumData->has_photo = $has_photo;
    }

    public function getIsActive()
    {
        return $this->navinumData->is_active;
    }

    public function setIsActive($is_active)
    {
        $this->navinumData->is_active = $is_active;
    }

    public function getLangueId()
    {
        return $this->navinumData->langue_id;
    }

    public function setLangueId($langue_id)
    {
        $this->navinumData->langue_id = $langue_id;
    }

    public function getNom()
    {
        return $this->navinumData->nom;
    }

    public function setNom($nom)
    {
        $this->navinumData->nom = $nom;
    }

    public function getNumMobile()
    {
        return $this->navinumData->num_mobile;
    }

    public function setNumMobile($num_mobile)
    {
        $this->navinumData->num_mobile = $num_mobile;
    }

    public function getPasswordSon()
    {
        return $this->navinumData->password_son;
    }

    public function setPasswordSon($password_son)
    {
        $this->navinumData->password_son = $password_son;
    }

    public function getPrenom()
    {
        return $this->navinumData->prenom;
    }

    public function setPrenom($prenom)
    {
        $this->navinumData->prenom = $prenom;
    }

    public function getPseudoSon()
    {
        return $this->navinumData->pseudo_son;
    }

    public function setPseudoSon($pseudo_son)
    {
        $this->navinumData->pseudo_son = $pseudo_son;
    }

    public function getTwitterId()
    {
        return $this->navinumData->twitter_id;
    }

    public function setTwitterId($twitter_id)
    {
        $this->navinumData->twitter_id = $twitter_id;
    }

    public function getType()
    {
        return $this->navinumData->type;
    }

    public function setType($type)
    {
        $this->navinumData->type = $type;
    }

    public function getUrlAvatar()
    {
        return $this->navinumData->url_avatar;
    }

    public function setUrlAvatar($url_avatar)
    {
        $this->navinumData->url_avatar = $url_avatar;
    }

    public function getVille()
    {
        return $this->navinumData->ville;
    }

    public function setVille($ville)
    {
        $this->navinumData->ville = $ville;
    }

    public function equals(UserInterface $user)
    {
        if (!$user instanceof NavinumUser) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}