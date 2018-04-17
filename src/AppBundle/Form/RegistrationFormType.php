<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 19/10/2017
 * Time: 11:46
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->remove('username');
    }

    public function getParent()
    {
        return BaseRegistrationFormType::class;
    }


}