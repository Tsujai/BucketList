<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped'=>false,
                'first_options'=>[
                    'label'=> 'Nouveau : ',
                    'attr'=>[
                        'placeholder'=>'Ecrire un autre mot de passe',
                    ],
                    'label_attr'=>[
                        'class'=>'string-detail',
                    ],
                    'constraints'=>[
                        new NotBlank([
                            'message'=>'Entrer un nouveau mot de passe',
                        ]),
                        new Length([
                            'min'=>4,
                            'minMessage'=>'Le mot de passe doit contenir au moins {{ limit }} caractères'
                        ]),
                    ],
                ],
                'second_options'=>[
                    'label'=>'Confirmer : ',
                    'attr'=>[
                        'placeholder'=>'Confirmer le mot de passe',
                    ],
                    'label_attr'=>[
                        'class'=>'string-detail',
                    ],
                ],
                'invalid_message'=>'Les mots de passe doivent correspondre',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
