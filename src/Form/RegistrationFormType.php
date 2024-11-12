<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;  // Add this line

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nome',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, digite seu nome',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, digite seu e-mail',
                    ]),
                    new Email([
                        'message' => 'Por favor, digite um e-mail válido',
                    ])
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Endereço',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, digite seu endereço',
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Eu concordo com os termos de uso',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Você precisa concordar com nossos termos.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Senha',
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => 'Digite sua senha'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, digite uma senha',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Sua senha deve ter pelo menos {{ limit }} caracteres',
                        'max' => 4096,
                        'maxMessage' => 'Sua senha é muito longa'
                    ]),
                ],
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