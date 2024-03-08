<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,[
//                'required'=>false,
                'label'=>'Email : ',
                'attr'=>[
                    'placeholder'=>'Ecrire un email',
                ],
                'label_attr'=>[
                    'class'=>'string-detail',
                ],
            ])
            ->add('pseudo',TextType::class,[
//                'required'=>false,
                'label'=>'Pseudo : ',
                'attr'=>[
                    'placeholder'=>'Ecrire un pseudo',
                ],
                'label_attr'=>[
                    'class'=>'string-detail',
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
//                'required'=>false,
                'mapped' => false,
                'label'=>'Cocher pour accepter : ',
                'label_attr'=>[
                    'class'=>'string-detail',
                ],
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les termes',
                    ]),
                ],
            ])

            ->add('plainPassword', RepeatedType::class, [
//                'required'=>false,
                'type'=> PasswordType::class,
                'label_attr'=>[
                    'hidden'=>'hidden'
                ],
                'options'=>[
                    'attr'=>[
                        'autocomplete'=>'new-password',
                    ],
                ],
                'first_options'=>[
                    'label'=> 'Mot de passe : ',
                    'attr'=>[
                        'placeholder'=>'Ecrire un mot de passe',
                    ],
                    'label_attr'=>[
                        'class'=>'string-detail',
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
                'mapped'=>false,
            ])

            ->add('submit', SubmitType::class,[
                'label'=>'Enregistrer',
                'attr'=>[
                    'class'=>'blue-button'
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
