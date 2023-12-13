<?php

namespace App\Form;

use App\Entity\Categorie;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cat_nom', TextType::class,[
                'attr' => [
                    'style' => 'background: #fff; border-radius: 15px;',
                    'class' => 'form-control place-color',
                    'placeholder' => 'Nom'
                ]
            ])
            ->add('cat_description', TextareaType::class,[
                'attr' => [
                    'style' => 'background: #fff; border-radius: 15px; height: 80px; margin-top: 10px; margin-bottom: 10px;',
                    'class' => 'form-control place-color',
                    'placeholder' => 'Description'
                ]
            ])
            ->add('cat_image', FileType::class,[
                'attr' => [
                    'style' => 'background: #fff; border-radius: 15px; margin-bottom: 10px;',
                    'class' => 'form-control place-color',
                    'placeholder' => 'Image',
                    'mapped' => false,
                ],
                'data_class' => null, // Important pour éviter l'erreur
            ])
            ->add('parent', EntityType::class,[
                'attr' => [
                    'style' => 'background: #fff; border-radius: 15px; margin-bottom: 10px;',
                    'class' => 'form-control place-color',
                ],
                'class' => Categorie::class,
                'choice_label' => 'cat_nom',
                'placeholder' => 'Sélectionner la catégorie parente',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.id = :Homme OR c.id = :Femme')
                        ->setParameter('Homme', 1) // Remplacez 1 par l'ID de la catégorie "Homme"
                        ->setParameter('Femme', 2); // Remplacez 2 par l'ID de la catégorie "Femme"
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
