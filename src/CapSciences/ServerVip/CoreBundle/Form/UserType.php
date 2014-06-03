<?php

namespace CapSciences\ServerVip\CoreBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo_son')
            ->add('email', 'email')
            ->add('password_son', 'repeated', array('type'     => 'password'))
            ->add('has_newsletter','checkbox', array('required'=>false,'label'=>'Newsletter signup'))
            ->add('has_cgu','checkbox', array('label'=>'J\'accepte les <a href="http://servervip.projects.clever-age.net/cgu/cgu-fr.html">conditions générales</a>'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'CapSciences\ServerVip\NavinumBundle\Model\Visiteur'));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'capsciences_servervip_core_user';
    }


}