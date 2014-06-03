<?php
namespace CapSciences\ServerVip\NavinumBundle\Providers;

class Visiteur extends ApiProvider
{
    const API_NAMESPACE = 'visiteur';

    /**
     * @param $id
     * @return mixed|null
     */
    public function find( $id )
    {
        return $this->get( self::API_NAMESPACE . '/' . $id );
    }

    /**
     * @param $username
     * @param $password
     * @param bool $allreadyEncoded
     * @return mixed|null
     */
    public function findByUsernameAndPassword( $username, $password, $allreadyEncoded = false )
    {
        if ( !$allreadyEncoded )
        {
            $password = md5( $password );
        }

        return $this->get( sprintf( '%s?pseudo_son=%s&password_son=%s', self::API_NAMESPACE, $username, $password ) );
    }

    /**
     * @param $facebookId
     * @return mixed|null
     */
    public function findByFacebookId( $facebookId )
    {
        $url = sprintf( '%s?facebook_id=%s', self::API_NAMESPACE, $facebookId );

        return $this->get( $url );
    }

    /**
     * @param $user
     * @return mixed
     * @throws \Exception
     */
    public function update( $user )
    {
        $data = $user->getNavinumData();
        unset( $data->created_at );
        unset( $data->updated_at );
        unset( $data->PreferenceMediaVisiteur );
        unset( $data->Contexte );
        unset( $data->Langue );

        /* @var $data->date_naissance DateTime */
        $data->date_naissance = $data->date_naissance->format( 'Y-m-d' );

        try
        {
            $result = $this->post( self::API_NAMESPACE . '/update', $data );
            if ( is_array( $result ) && count( $result ) == 1 )
            {
                return $result[0];
            }
        }
        catch ( \Exception $e )
        {
            throw $e;
        }


    }

    /**
     * Visitor creation webservice call
     * @param \CapSciences\ServerVip\NavinumBundle\Model\Visiteur $visiteur
     * @param $isFacebook
     * @throws \Exception
     * @return mixed
     */
    public function create( \CapSciences\ServerVip\NavinumBundle\Model\Visiteur $visiteur, $isFacebook )
    {
        try
        {
            $result = null;
            if ( $isFacebook )
            {
                $data = array(
                    "email" => $visiteur->getEmail(),
                    "pseudo_son" => $visiteur->getPseudoSon(),
                    "nom" => $visiteur->getNom(),
                    "prenom" => $visiteur->getPrenom(),
                    "type" => "visiteur",
                    "facebook_id" => $visiteur->getFacebookId(),
                    "has_newsletter" => false,
                    "contexte_creation_id" => "59DDDF5653D1D1F62B5BD482880B16BA",
                    "is_active" => true
                );
                $result = $this->post(
                    self::API_NAMESPACE . '/createFacebook',
                    $data,
                    'array<CapSciences\ServerVip\NavinumBundle\Model\Visiteur>'
                );
            }
            else
            {
                $data = array(
                    "email" => $visiteur->getEmail(),
                    "pseudo_son" => $visiteur->getPseudoSon(),
                    "password_son" => $visiteur->getPasswordSon(),
                    "type" => "visiteur",
		    "contexte_creation_id" => "59DDDF5653D1D1F62B5BD482880B16BA",
                    "has_newsletter" => $visiteur->getHasNewsletter(),
                    "is_active" => false
                );
                $result = $this->post(
                    self::API_NAMESPACE . '/create',
                    $data,
                    'array<CapSciences\ServerVip\NavinumBundle\Model\Visiteur>'
                );
            }


            if ( is_array( $result ) && count(
                    $result
                ) == 1 && $result[0] instanceof \CapSciences\ServerVip\NavinumBundle\Model\Visiteur
            )
            {
                /** @var \CapSciences\ServerVip\NavinumBundle\Model\Visiteur $newVisiteur */
                $newVisiteur = $result[0];
                $visiteur->setGuid( $newVisiteur->getGuid() );

                return true;
            }

            return $result;
        }
        catch ( \Exception $e )
        {
            throw $e;
        }
    }

    /**
     * @param $guid
     * @return mixed
     * @throws \Exception
     */
    public function activate( $guid )
    {
        try
        {
            $result = $this->get( self::API_NAMESPACE . '/enabled?guid=' . $guid );
            if ( is_array( $result ) && count( $result ) == 1 )
            {
                return $result[0];
            }
        }
        catch ( \Exception $e )
        {
            throw $e;
        }
    }

    /**
     * @param $guid
     * @return mixed
     * @throws \Exception
     */
    public function delete( $guid )
    {
        try
        {
            $result = $this->get( self::API_NAMESPACE . '/delete?guid=' . $guid );
            if ( is_array( $result ) && count( $result ) == 1 )
            {
                return $result[0];
            }
        }
        catch ( \Exception $e )
        {
            throw $e;
        }
    }

    /**
     * @param $guid
     * @return mixed
     * @throws \Exception
     */
    public function disable( $guid )
    {
        try
        {
            $result = $this->get( self::API_NAMESPACE . '/disabled?guid=', $guid );
            if ( is_array( $result ) && count( $result ) == 1 )
            {
                return $result[0];
            }
        }
        catch ( \Exception $e )
        {
            throw $e;
        }
    }

    /**
     * @param $guid
     * @param $photo
     */
    public function sendPhoto( $guid, $photo )
    {
        try
        {
            $data = array(
                "guid" => $guid,
                "photo" => $photo
            );

            return $this->post(
                self::API_NAMESPACE . '/sendPhoto',
                $data
            );
        }
        catch ( \Exception $e )
        {
            throw $e;
        }
    }
}
