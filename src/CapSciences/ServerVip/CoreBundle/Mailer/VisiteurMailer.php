<?php
/**
 * User: tperrin
 * Date: 23/04/13
 * Time: 17:50
 */

namespace CapSciences\ServerVip\CoreBundle\Mailer;


use CapSciences\ServerVip\CoreBundle\Mailer;
use CapSciences\ServerVip\NavinumBundle\Model\Visiteur;

class VisiteurMailer extends Mailer
{
    public function sendActivation(Visiteur $visiteur)
    {
        $message = \Swift_Message::newInstance(
                $this->container->getParameter('mail_activation_subject'), null, 'text/html','utf-8'
            )
            ->setTo($visiteur->getEmail())
            ->setBody(
                $this->renderView(
                    $this->container->getParameter('mail_activation_template'),
                    array(
                        'url' => $this->generateUrl(
                            'capsciences_servervip_core_security_activate',
                            array('guid' => $visiteur->getGuid()),
                            true
                        ),
                        'pseudo' => $visiteur->getPseudoSon()
                    )
                )
            );
        $this->send($message);
    }
}