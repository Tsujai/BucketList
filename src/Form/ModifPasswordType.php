<?php

namespace App\Form;

use App\Entity\User;
use PHPUnit\Framework\Constraint\IsTrue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Expression;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ModifPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,[
                'label' => 'Email :',
                'label_attr'=>[
                    'class'=>'string-detail',
                ],
                'attr'=>[
                    'readonly' => 'readonly',
                ],
            ])
            ->add('pseudo',TextType::class,[
                'label' => 'Pseudo : ',
                'label_attr'=>[
                    'class'=>'string-detail',
                ],
            ])
            ->add('mdpCheckbox',CheckboxType::class,[
                'mapped'=>false,
                'required' => false,
                'label' => 'Modifier le mot de passe ?',
                'label_attr'=>[
                    'class'=>'string-detail',
                ],
                'attr'=>[
                    'class'=>'switch',
                ],
            ])
            ->add('oldPassword',PasswordType::class,[
                'mapped'=>false,
                'label' => 'Actuel :',
                'label_attr'=>[
                    'class'=>'string-detail',
                    'id'=>'label_old_password'
                ],
                'attr'=>[
                    'placeholder' => 'Ecrire votre ancien mot de passe',
                ],
            ])
            ->add('newPassword',RepeatedType::class,[
                'required' => false,
                'type' => PasswordType::class,
                'mapped'=>false,
                'first_options'=>[
                    'label'=> 'Nouveau : ',
                    'attr'=>[
                        'placeholder'=>'Ecrire un autre mot de passe',
                        'style' => 'display: none',
                        'disabled' => 'disabled'
                    ],
                    'label_attr'=>[
                        'class'=>'string-detail',
                        'style' => 'display: none',
                        'id'=>'label_first'
                    ],
                    'constraints'=>[
                        new NotBlank([
                            'groups'=>['changePassword'],
                            'message'=>'Entrer un nouveau mot de passe',
                        ]),
                        new Length([
                            'groups'=>['changePassword'],
                            'min'=>4,
                            'minMessage'=>'Le mot de passe doit contenir au moins {{ limit }} caractères'
                        ]),
                    ],
                ],
                'second_options'=>[
                    'label'=>'Confirmer : ',
                    'attr'=>[
                        'placeholder'=>'Confirmer le mot de passe',
                        'style' => 'display: none',
                        'disabled' => 'disabled'
                    ],
                    'label_attr'=>[
                        'class'=>'string-detail',
                        'style' => 'display: none',
                        'id'=>'label_second'
                    ],
                ],
                'invalid_message'=>'Les mots de passe doivent correspondre',
            ])
            ->add('submit',SubmitType::class,[
                'label' => 'Modifier',
                'attr'=>[
                    'class' => 'modify-button'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
