<?php
/**
 * Created by PhpStorm.
 * User: neboj
 * Date: 29/1/2018
 * Time: 13:40
 */

namespace AppBundle\Form;


use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('profile_image',FileType::class,array('label'=>'insert an image'))
            ->add('save',SubmitType::class);
    }

    public function getName()
    {
        return 'user';
    }
}