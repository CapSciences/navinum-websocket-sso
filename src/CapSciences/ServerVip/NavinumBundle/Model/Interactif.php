<?php

namespace CapSciences\ServerVip\NavinumBundle\Model;

use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\AccessType;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Expose;

/**
 * Class Interactif
 * @package CapSciences\ServerVip\NavinumBundle\Model
 */
class Interactif {

    /**
     * @Type("string")
     */
    protected $guid;
    /**
     * @Type("string")
     */
    protected $libelle;
    /**
     * @Accessor(getter="getSourceType",setter="setSourceType")
     * @Type("string")
     */
    protected $source_type;
    /**
     * @Type("string")
     */
    protected $synopsis;
    /**
     * @Type("string")
     */
    protected $description;
    /**
     * @Type("string")
     */
    protected $logo;
    /**
     * @Type("string")
     */
    protected $categorie;
    /**
     * @Type("string")
     */
    protected $version;
    /**
     * @Type("string")
     */
    protected $editeur;
    /**
     * @Type("string")
     */
    protected $publics;
    /**
     * @Type("string")
     */
    protected $markets;
    /**
     * @Accessor(getter="getUrlMarketIos",setter="setUrlMarketIos")
     * @Type("string")
     */
    protected $url_market_ios;
    /**
     * @Accessor(getter="getUrlMarketAndroid",setter="setUrlMarketAndroid")
     * @Type("string")
     * 
     */
    protected $url_market_android;
    /**
     * @Accessor(getter="getUrlMarketWindows",setter="setUrlMarketWindows")
     * @Type("string")
     */
    protected $url_market_windows;
    /**
     * @Type("string")
     */
    protected $langues;
    /**
     * @Type("string")
     */
    protected $image1;
    /**
     * @Type("string")
     */
    protected $image2;
    /**
     * @Type("string")
     */
    protected $image3;
    /**
     * @Accessor(getter="getDateDiff",setter="setDateDiff")
     * @Type("string")
     */
    protected $date_diff;
    /**
     * @Accessor(getter="getUrlMarketWindows",setter="setUrlMarketWindows")
     * @Type("string")
     */
    protected $explications_resultats;
    /**
     * @Type("string")
     */
    protected $score;
    /**
     * @Type("string")
     */
    protected $variable;
    /**
     * @Accessor(getter="getUrlScheme",setter="setUrlScheme")
     * @Type("string")
     */
    protected $url_scheme;
    /**
     * @Accessor(getter="getUrlFichierInteractif",setter="setUrlFichierInteractif")
     * @Type("string")
     */
    protected $url_fichier_interactif;
    /**
     * @Accessor(getter="getUrlPierreDeRosette",setter="setUrlPierreDeRosette")
     * @Type("string")
     */
    protected $url_pierre_de_rosette;
    /**
     * @Accessor(getter="getUrlIllustration",setter="setUrlIllustration")
     * @Type("string")
     */
    protected $url_illustration;
    /**
     * @Accessor(getter="getRefreshDeploiement",setter="setRefreshDeploiement")
     * @Type("string")
     */
    protected $refresh_deploiement;
    /**
     * @Accessor(getter="getIsVisiteurNeeded",setter="setIsVisiteurNeeded")
     * @Type("string")
     */
    protected $is_visiteur_needed;
    /**
     * @Accessor(getter="getIsLogvisiteNeeded",setter="setIsLogvisiteNeeded")
     * @Type("string")
     */
    protected $is_logvisite_needed;
    /**
     * @Accessor(getter="getIsLogvisiteVerboseNeeded",setter="setIsLogvisiteVerboseNeeded")
     * @Type("string")
     */
    protected $is_logvisite_verbose_needed;
    /**
     * @Accessor(getter="getIsParcoursNeeded",setter="setIsParcoursNeeded")
     * @Type("string")
     */
    protected $is_parcours_needed;
    /**
     * @Type("string")
     */
    protected $ordre;
    /**
     * @Accessor(getter="getUrlVisiteurNeeded",setter="setUrlVisiteurNeeded")
     * @Type("string")
     */
    protected $url_visiteur_needed;
    /**
     * @Accessor(getter="getUrlLogvisiteNeeded",setter="setUrlLogvisiteNeeded")
     * @Type("string")
     */
    protected $url_logvisite_needed;
    /**
     * @Accessor(getter="getUrlLogvisiteVerboseNeeded",setter="setUrlLogvisiteVerboseNeeded")
     * @Type("string")
     */
    protected $url_logvisite_verbose_needed;
    /**
     * @Accessor(getter="getUrlParcoursNeeded",setter="setUrlParcoursNeeded")
     * @Type("string")
     */
    protected $url_parcours_needed;

    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;
    }

    public function getCategorie()
    {
        return $this->categorie;
    }

    public function setDateDiff($date_diff)
    {
        $this->date_diff = $date_diff;
    }

    public function getDateDiff()
    {
        return $this->date_diff;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setEditeur($editeur)
    {
        $this->editeur = $editeur;
    }

    public function getEditeur()
    {
        return $this->editeur;
    }

    public function setExplicationsResultats($explications_resultats)
    {
        $this->explications_resultats = $explications_resultats;
    }

    public function getExplicationsResultats()
    {
        return $this->explications_resultats;
    }

    public function setGuid($guid)
    {
        $this->guid = $guid;
    }

    public function getGuid()
    {
        return $this->guid;
    }

    public function setImage1($image1)
    {
        $this->image1 = $image1;
    }

    public function getImage1()
    {
        return $this->image1;
    }

    public function setImage2($image2)
    {
        $this->image2 = $image2;
    }

    public function getImage2()
    {
        return $this->image2;
    }

    public function setImage3($image3)
    {
        $this->image3 = $image3;
    }

    public function getImage3()
    {
        return $this->image3;
    }

    public function setIsLogvisiteNeeded($is_logvisite_needed)
    {
        $this->is_logvisite_needed = $is_logvisite_needed;
    }

    public function getIsLogvisiteNeeded()
    {
        return $this->is_logvisite_needed;
    }

    public function setIsLogvisiteVerboseNeeded($is_logvisite_verbose_needed)
    {
        $this->is_logvisite_verbose_needed = $is_logvisite_verbose_needed;
    }

    public function getIsLogvisiteVerboseNeeded()
    {
        return $this->is_logvisite_verbose_needed;
    }

    public function setIsParcoursNeeded($is_parcours_needed)
    {
        $this->is_parcours_needed = $is_parcours_needed;
    }

    public function getIsParcoursNeeded()
    {
        return $this->is_parcours_needed;
    }

    public function setIsVisiteurNeeded($is_visiteur_needed)
    {
        $this->is_visiteur_needed = $is_visiteur_needed;
    }

    public function getIsVisiteurNeeded()
    {
        return $this->is_visiteur_needed;
    }

    public function setLangues($langues)
    {
        $this->langues = $langues;
    }

    public function getLangues()
    {
        return $this->langues;
    }

    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    }

    public function getLibelle()
    {
        return $this->libelle;
    }

    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function setMarkets($markets)
    {
        $this->markets = $markets;
    }

    public function getMarkets()
    {
        return $this->markets;
    }

    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;
    }

    public function getOrdre()
    {
        return $this->ordre;
    }

    public function setPublics($publics)
    {
        $this->publics = $publics;
    }

    public function getPublics()
    {
        return $this->publics;
    }

    public function setRefreshDeploiement($refresh_deploiement)
    {
        $this->refresh_deploiement = $refresh_deploiement;
    }

    public function getRefreshDeploiement()
    {
        return $this->refresh_deploiement;
    }

    public function setScore($score)
    {
        $this->score = $score;
    }

    public function getScore()
    {
        return $this->score;
    }

    public function setSourceType($source_type)
    {
        $this->source_type = $source_type;
    }

    public function getSourceType()
    {
        return $this->source_type;
    }

    public function setSynopsis($synopsis)
    {
        $this->synopsis = $synopsis;
    }

    public function getSynopsis()
    {
        return $this->synopsis;
    }

    public function setUrlFichierInteractif($url_fichier_interactif)
    {
        $this->url_fichier_interactif = $url_fichier_interactif;
    }

    public function getUrlFichierInteractif()
    {
        return $this->url_fichier_interactif;
    }

    public function setUrlIllustration($url_illustration)
    {
        $this->url_illustration = $url_illustration;
    }

    public function getUrlIllustration()
    {
        return $this->url_illustration;
    }

    public function setUrlLogvisiteNeeded($url_logvisite_needed)
    {
        $this->url_logvisite_needed = $url_logvisite_needed;
    }

    public function getUrlLogvisiteNeeded()
    {
        return $this->url_logvisite_needed;
    }

    public function setUrlLogvisiteVerboseNeeded($url_logvisite_verbose_needed)
    {
        $this->url_logvisite_verbose_needed = $url_logvisite_verbose_needed;
    }

    public function getUrlLogvisiteVerboseNeeded()
    {
        return $this->url_logvisite_verbose_needed;
    }

    public function setUrlMarketAndroid($url_market_android)
    {
        $this->url_market_android = $url_market_android;
    }

    public function getUrlMarketAndroid()
    {
        return $this->url_market_android;
    }

    public function setUrlMarketIos($url_market_ios)
    {
        $this->url_market_ios = $url_market_ios;
    }

    public function getUrlMarketIos()
    {
        return $this->url_market_ios;
    }

    public function setUrlMarketWindows($url_market_windows)
    {
        $this->url_market_windows = $url_market_windows;
    }

    public function getUrlMarketWindows()
    {
        return $this->url_market_windows;
    }

    public function setUrlParcoursNeeded($url_parcours_needed)
    {
        $this->url_parcours_needed = $url_parcours_needed;
    }

    public function getUrlParcoursNeeded()
    {
        return $this->url_parcours_needed;
    }

    public function setUrlPierreDeRosette($url_pierre_de_rosette)
    {
        $this->url_pierre_de_rosette = $url_pierre_de_rosette;
    }

    public function getUrlPierreDeRosette()
    {
        return $this->url_pierre_de_rosette;
    }

    public function setUrlScheme($url_scheme)
    {
        $this->url_scheme = $url_scheme;
    }

    public function getUrlScheme()
    {
        return $this->url_scheme;
    }

    public function setUrlVisiteurNeeded($url_visiteur_needed)
    {
        $this->url_visiteur_needed = $url_visiteur_needed;
    }

    public function getUrlVisiteurNeeded()
    {
        return $this->url_visiteur_needed;
    }

    public function setVariable($variable)
    {
        $this->variable = $variable;
    }

    public function getVariable()
    {
        return $this->variable;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function getVersion()
    {
        return $this->version;
    }
}