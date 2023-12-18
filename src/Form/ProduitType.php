<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Fournisseur;
use App\Entity\Produit;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pro_nom', TextType::class, [
                'attr' => [
                    'style' => 'background: #fff; border-radius: 15px;',
                    'class' => 'form-control place-color',
                    'placeholder' => 'Nom'
                ]
            ])
            ->add('pro_description', TextType::class, [
                'attr' => [
                    'style' => 'background: #fff; border-radius: 15px; margin-top: 10px; margin-bottom: 10px;',
                    'class' => 'form-control place-color',
                    'placeholder' => 'Description'
                ]
            ])
            ->add('pro_stock', IntegerType::class, [
                'attr' => [
                    'style' => 'background: #fff; border-radius: 15px;',
                    'class' => 'form-control place-color',
                    'placeholder' => 'Stock'
                ]
            ])
            ->add('prix_achat', MoneyType::class, [
                'currency' => false,
                'constraints' => [
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'Le prix d\'achat doit être supérieur à zéro.',
                    ]),
                ],
                'attr' => [
                    'style' => 'background: #fff; border-radius: 15px; margin-top: 10px; margin-bottom: 10px;',
                    'class' => 'form-control place-color',
                    'placeholder' => 'Prix d\'achat'
                ]
            ])
            ->add('prix_vente', MoneyType::class, [
                'currency' => false,
                'constraints' => [
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'Le prix de vente doit être supérieur à zéro.',
                    ]),
                ],
                'attr' => [
                    'style' => 'background: #fff; border-radius: 15px;',
                    'class' => 'form-control place-color',
                    'placeholder' => 'Prix de vente'
                ]
            ])
            ->add('image', FileType::class, [
                'attr' => [
                    'style' => 'background: #fff; border-radius: 15px; margin-top: 10px; margin-bottom: 10px;',
                    'class' => 'form-control place-color',
                    'mapped' => false,
                ],
                'data_class' => null, // Important pour éviter l'erreur
            ])
            ->add('is_active', CheckboxType::class, [
                'attr' => [
                    'style' => 'background: #fff; border-radius: 15px;margin-left : 80px;',
                    'class' => '',
                    ]
            ])
//            ->add('slug', TextType::class, [
//                'attr' => [
//                    'style' => 'background: #fff; border-radius: 15px;',
//                    'class' => 'form-control place-color',
//                    'placeholder' => 'Nom'
//                ]
//            ])
            ->add('categorie', EntityType::class, [
                'attr' => [
                    'style' => 'background: #fff; border-radius: 15px; margin-top: 10px; margin-bottom: 10px;',
                    'class' => 'form-control place-color',
                    'placeholder' => 'Categorie'
                ],
                'class' => Categorie::class,
                'choice_label' => 'cat_nom',
                'placeholder' => 'Sélectionner la catégorie du produit',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.id != :Homme OR c.id != :Femme')
                        ->setParameter('Homme', 1) // Remplacez 1 par l'ID de la catégorie "Homme"
                        ->setParameter('Femme', 2); // Remplacez 2 par l'ID de la catégorie "Femme"
                },
            ])
            ->add('fournisseur', EntityType::class, [
                'attr' => [
                    'style' => 'background: #fff; border-radius: 15px; margin-bottom: 10px;',
                    'class' => 'form-control place-color',
                ],
                'class' => Fournisseur::class,
                'placeholder' => 'Sélectionner le fournisseur',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
