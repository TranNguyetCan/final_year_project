<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\FileType;
class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('username', HiddenType::class)
        ->add('fullname', TextType::class)
            ->add('birthday',DateType::class,[
                'widget'=>'single_text'
            ])
            ->add('gender', ChoiceType::class, 
                array(
                    'choices' => array(
                    'Male' => 0,
                   ' Female' => 1
                ),
                'multiple' => false,
                'expanded' => true
            ))

            
            ->add('phone', TextType::class)
            ->add('address', TextType::class)
            ->add('avatar', FileType::class, [ // Thêm trường tải lên ảnh đại diện
                'label' => 'Avatar', // Nhãn cho trường
                'mapped' => false, // Không ánh xạ trường này với một thuộc tính của đối tượng
                'required' => false, // Trường không bắt buộc
            ])
            ->add('save',SubmitType::class,[
                'label' => "save"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
