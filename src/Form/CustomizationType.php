<?php
// src/Form/CustomizationType.php
namespace App\Form;

use App\Entity\Customization;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomizationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('flavor', ChoiceType::class, [
                'label' => 'Sabor',
                'choices' => [
                    'Chocolate' => 'chocolate',
                    'Baunilha' => 'vanilla',
                    'Morango' => 'strawberry',
                    'Red Velvet' => 'red_velvet'
                ],
                'required' => true
            ])
            ->add('topping', ChoiceType::class, [
                'label' => 'Cobertura',
                'choices' => [
                    'Chocolate (+R$ 3,00)' => 'chocolate',
                    'Chantilly (+R$ 2,00)' => 'whipped_cream',
                    'Cream Cheese (+R$ 3,00)' => 'cream_cheese'
                ],
                'required' => true
            ])
            ->add('decoration', ChoiceType::class, [
                'label' => 'Decoração',
                'choices' => [
                    'Granulado (+R$ 2,00)' => 'sprinkles',
                    'Frutas (+R$ 4,00)' => 'fruits',
                    'Confeitos (+R$ 2,00)' => 'confetti'
                ],
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customization::class,
        ]);
    }
}