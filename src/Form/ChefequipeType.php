<?php
namespace App\Form;

use App\Entity\Chefequipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints as Assert;

class ChefequipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500',
                    'placeholder' => 'Entrez le nom',
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500',
                    'placeholder' => 'Entrez le prénom',
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('cin', TextType::class, [
                'label' => 'CIN',
                'attr' => [
                    'class' => 'w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500',
                    'placeholder' => 'Enter CIN (8 digits)',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'CIN must be exactly {{ limit }} digits long',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+$/',
                        'message' => 'CIN must contain only numbers.',
                    ]),
                ],
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'class' => 'w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500',
                    'placeholder' => 'Enter phone number (8 digits)',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'Phone number must be exactly {{ limit }} digits long',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+$/',
                        'message' => 'Phone number must contain only numbers.',
                    ]),
                ],
            ])
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
                'attr' => [
                    'class' => 'w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500',
                    'placeholder' => 'Enter username',
                ],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => [
                    'class' => 'w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500',
                    'placeholder' => 'Enter password',
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 6]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chefequipe::class,
        ]);
    }
}
