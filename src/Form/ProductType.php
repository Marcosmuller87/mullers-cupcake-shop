<?php
namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nome',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, digite o nome do produto',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Descrição',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, adicione uma descrição',
                    ]),
                ],
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Preço',
                'currency' => 'BRL',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, digite o preço',
                    ]),
                    new Positive([
                        'message' => 'O preço deve ser maior que zero',
                    ]),
                ],
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Imagem do Produto',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Por favor, envie uma imagem válida (JPG ou PNG)',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}