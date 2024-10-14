<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\Material;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('material', EntityType::class, [
            'class' => Material::class,
            'choice_label' => function (Material $material) {
                return $material->getName();
            },
            // 'placeholder' => 'Select a voucher',
            // 'required' => false,
        ])
            ->add('name', TextType::class)
            ->add('quantity', NumberType::class)
            ->add('unit', TextType::class)
            ->add('price', NumberType::class)
            ->add('inventory', TextType::class)
            ->add('image', FileType::class, [
                'data_class' => null
            ])
            
            ->add('save',SubmitType::class,[
                'label' => "Add"
            ]); 

            // public function buildForm(FormBuilderInterface $builder, array $options): void
            // {
            //     $builder
            //         ->add('name', TextType::class, array(
            //             'constraints' => new Length(array('min' => 3))
            //         ))
            //         ->add('descriptions', TextType::class, array(
            //             // 'constraints' => new Length(array('min' => 30))
            //         ))
            //         ->add(
            //             'status',
            //             ChoiceType::class,
            //             array(
            //                 'choices' => array(
            //                     // So nut radio button
            //                     'On Sale' => '0',
            //                     'Sold Out' => '1'
            //                 ),
        
            //                 // Cho chon nhieu hay khong
            //                 'multiple' => false,
            //                 'expanded' => true,
            //                 'data' => 0
            //             )
            //         )
            //         ->add('descriptions', TextType::class)
            //         ->add('price', TextType::class)
            //         // ->add(
            //         //     'forGender',
            //         //     ChoiceType::class,
            //         //     array(
            //         //         'choices' => array(
            //         //             // So nut radio button
            //         //             ' Mens Clothing' => '0',
            //         //             'Womens Clothing' => '1'
            //         //         ),
        
            //         //         // Cho chon nhieu hay khong
            //         //         'multiple' => false,
            //         //         'expanded' => true
            //         //     )
            //         // )
            //         ->add('image', FileType::class, array('data_class' => null))
            //         ->add('category', EntityType::class, [
            //             'class' => Category::class,
            //             'choice_label' => 'name',
            //             'placeholder' => 'Choose an option'
            //         ])
            //         ->add('supplier', EntityType::class, [
            //             'class' => Supplier::class,
            //             'choice_label' => 'name',
            //             'placeholder' => 'Choose an option'
            //         ])
            //         ->add('save', SubmitType::class, [
            //             'label' => "Next"
            //         ]);
            // }
        
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);
    }
}
