<?php

namespace App\Form;

use App\Entity\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Entity\Voucher;
use App\Enum\OrderStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Choice;

class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cusName', TextType::class)
            ->add('cusPhone', TextType::class)
            ->add('deliveryLocal', TextType::class)
            ->add('total', NumberType::class)
            // ->add('status', HiddenType::class, array(
            //     'data' => "ordered"
            // ))
            ->add('vouchers', EntityType::class, [
                'class' => Voucher::class,
                'choice_label' => function (Voucher $voucher) {
                    return $voucher->getPercentage() . '%';
                },
                // 'placeholder' => 'Select a voucher',
                // 'required' => false,
            ])
            
            ->add('paymentMethod', ChoiceType::class, [
                'choices' => [
                        'Paypal' => 'paypal',
                        'Cash on delivery' => 'COD'
                ],
                'label' => false,
                'required' => true,
                'multiple' => false,
                'expanded' => true 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
