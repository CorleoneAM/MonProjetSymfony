<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use App\Entity\User;

class UserType extends AbstractType
{
   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       
        $builder
            ->add('email',EmailType::class, [
                'attr'=>[
                    'placeholder' => 'Your email..',
                    'style' =>'width: 100%; padding: 12px 20px;
                    margin: 8px 0; display: inline-block; border: 1px solid #ccc;
                     border-radius: 4px; box-sizing: border-box;'
                ]
            ])
            ->add('firstName',TextType::class , [
                'attr'=>[
                    'placeholder' => 'Your name..',
                    'style' =>'width: 100%; padding: 12px 20px;
                    margin: 8px 0; display: inline-block; border: 1px solid #ccc;
                     border-radius: 4px; box-sizing: border-box;'
                ]
            ])
            ->add('password',PasswordType::class , [
                'attr'=>[
                    'placeholder' => 'Your password..',
                    'style' =>'width: 100%; padding: 12px 20px;
                    margin: 8px 0; display: inline-block; border: 1px solid #ccc;
                     border-radius: 4px; box-sizing: border-box;'
                ]
            ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            array('data_class'=>User::class)
        ]);
    }
}