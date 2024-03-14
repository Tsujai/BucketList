<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Wish;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CreateWishType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,[
                'label'=>'Titre : ',
                'label_attr'=>[
                    'class'=>'string-detail',
                ],
                'attr'=>[
                    'placeholder'=>'Ecrire le titre du souhait',
                    'size'=>50,
                ]
            ])

            ->add('description', TextareaType::class,[
                'required'=> false,
                'label'=>'Description : ',
                'label_attr'=>[
                    'class'=>'string-detail',
                ],
                'attr'=>[
                    'placeholder'=>'Ecrire la description du souhait',
                    'rows'=>4,
                    'cols'=>53,
                ]
            ])

            ->add('auteur', TextType::class,[
                'mapped'=>false,
                'label'=>'Auteur : ',
                'label_attr'=>[
                    'class'=>'string-detail',
                ],
                'attr'=>[
                    'placeholder'=>'Ecrire votre nom',
                    'size'=>50,
                    'readonly'=>'readonly'
                ],
                'data' => $options['pseudo'],
            ])

            ->add('icon', TextType::class,[
                'label'=>'Emoticone : ',
                'label_attr'=>[
                    'class'=>'string-detail',
                ],
                'attr'=>[
                    'placeholder'=>'Choisir un émoticone',
                    'size'=>10,
                ]
            ])

            ->add('submit', SubmitType::class,[
                'label'=>'Enregistrer',
                'attr'=>[
                    'class'=>'blue-button',
                ]
            ])

            ->add('isPublished',CheckboxType::class,[
                'required'=> false,
                'label'=>'Publier ce souhait ?',
                'label_attr'=>[
                    'class'=>'string-detail',
                ],
                'attr'=>[
                    'checked'=>'checked',
                    'class'=>'switch',
                ]
            ])

            ->add('isDeleted',CheckboxType::class,[
                'mapped'=>false,
                'required'=>false,
                'label'=>'Supprimer l\'image ?',
                'label_attr'=>[
                    'class'=>'string-detail',
                ],
                'attr'=>[
                    'class'=>'switch',
                ],
            ])

            ->add('picture',FileType::class,[
                'required'=>false,
                'mapped'=>false,
                'label'=>'Image : ',
                'label_attr'=>[
                    'class'=>'string-detail',
                ],
                'constraints'=>[
                    new File([
                        'maxSize'=>'1024K',
                        'mimeTypes'=>[
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage'=>'Ce format n\'est pas pris en charge. Formats autorisés : ',
                        'maxSizeMessage'=>'Ce fichier est trop lourd',
                    ])
                ]
            ])

            ->add('category',EntityType::class,[
                'class'=>Category::class,
                'placeholder'=>'Choisir une catégorie',
                'attr'=>[
                    'class'=>'form-create-widget'
                ],
                'choice_label'=>'name',
                'label_attr'=>[
                    'class'=>'string-detail',
                ],
                'label'=>'Catégorie : ',
                'query_builder'=> function(CategoryRepository $categoryRepository){
                    return $categoryRepository->createQueryBuilder('cat')->addOrderBy('cat.name','ASC');
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wish::class,
            'pseudo' => null,
        ]);
    }
}
