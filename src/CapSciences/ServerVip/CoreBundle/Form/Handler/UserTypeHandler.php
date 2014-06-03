<?php

namespace CapSciences\ServerVip\CoreBundle\Form\Handler;


use CapSciences\ServerVip\CoreBundle\Mailer\VisiteurMailer;
use CapSciences\ServerVip\NavinumBundle\Model\Visiteur;
use CapSciences\ServerVip\NavinumBundle\Providers\Visiteur as VisiteurProvider;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class UserTypeHandler
{
    /**
     * @var \CapSciences\ServerVip\NavinumBundle\Providers\Visiteur
     */
    protected $visiteurProvider;

    /**
     * @var \CapSciences\ServerVip\CoreBundle\Mailer\VisiteurMailer
     */
    protected $mailer;

    public function __construct(VisiteurProvider $visiteurProvider, VisiteurMailer $mailer)
    {
        $this->visiteurProvider = $visiteurProvider;
        $this->mailer = $mailer;
    }

    public function handle(FormInterface $form, Visiteur $visiteur)
    {
        $result = $this->visiteurProvider->create($visiteur);

        if($result !== true) {
            $form->addError(new FormError($result[0]->message));
                return false;
        }

        $this->mailer->sendActivation($visiteur);

        return true;
    }


}