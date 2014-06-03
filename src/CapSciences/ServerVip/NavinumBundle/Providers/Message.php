<?php

namespace CapSciences\ServerVip\NavinumBundle\Providers;


class Message extends ApiProvider{
    const API_NAMESPACE = 'sentMessage';

    /**
     * @param $data
     * @return mixed
     */
    public function resetUserPassword($login,$mail)
    {
        $data = array(
            'mail'=>$mail,
            'pseudo'=>$login
        );
        try{
            $result = $this->post(self::API_NAMESPACE.'/resetPassword', $data);
            if(is_array($result) && count($result) == 1) {
                return $result[0];
            }
        }catch (\Exception $e){
            throw $e;
        }

    }

    /**
     * @param $login
     * @param $mobile
     * @return mixed
     */
    public function resetUserPasswordSms($login,$mobile)
    {

//        $mobile='33'.intval(preg_replace('/[a-zA-Z ]{1}||_||\.||-||\//','',$mobile));
        $mobile=preg_replace('/[a-zA-Z ]{1}||_||\.||-||\//','',$mobile);

        $data = array(
            'num_mobile'=>$mobile,
            'pseudo'=>$login
        );
        $result = $this->post(self::API_NAMESPACE.'/resetPasswordSms', $data);
        if(is_array($result) && count($result) == 1) {
            return $result[0];
        }
    }

    /**
     * @param $mail
     * @return mixed
     */
    public function sendPseudo($mail){
        $data = array(
            'mail'=>$mail,
        );
        try{
            $result = $this->post(self::API_NAMESPACE.'/sendPseudo', $data);
            if(is_array($result) && count($result) == 1) {
                return $result[0];
            }
        }catch (\Exception $e){
            throw $e;
        }
    }

    /**
     * @param $mail
     * @param $mobile
     * @return mixed
     */
    public function sendPseudoSms($mail,$mobile){
        $mobile=preg_replace('/[a-zA-Z ]{1}||_||\.||-||\//','',$mobile);
//        $mobile='33'.intval(preg_replace('/[a-zA-Z ]{1}||_||\.||-||\//','',$mobile));
        $data = array(
            'mail'=>$mail,
            'num_mobile'=>$mobile
        );
        $result = $this->post(self::API_NAMESPACE.'/sendPseudoSms', $data);
        if(is_array($result) && count($result) == 1) {
            return $result[0];
        }
    }
}