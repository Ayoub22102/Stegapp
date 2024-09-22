<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', EmailType::class, [
                'label' => 'Adresse e-mail',
                'help' => 'Entrez une adresse e-mail valide.',
                'attr' => [
                    'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md',
                    'placeholder' => 'Entrez l\'adresse e-mail',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(['message' => 'Veuillez entrer une adresse e-mail valide.']),
                ]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'help' => 'Entrez votre nom.',
                'attr' => [
                    'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md',
                    'placeholder' => 'Entrez votre nom',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Votre nom doit comporter au moins {{ limit }} caractères',
                        'maxMessage' => 'Votre nom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'help' => 'Entrez votre prénom.',
                'attr' => [
                    'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md',
                    'placeholder' => 'Entrez votre prénom',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Votre prénom doit comporter au moins {{ limit }} caractères',
                        'maxMessage' => 'Votre prénom ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ]
            ])
            ->add('cin', TextType::class, [
                'label' => 'CIN',
                'help' => 'Entrez votre CIN (8 chiffres).',
                'attr' => [
                    'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md',
                    'placeholder' => 'Entrez votre CIN',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'CIN doit comporter exactement {{ limit }} chiffres',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+$/',
                        'message' => 'CIN ne doit contenir que des chiffres.',
                    ]),
                ]
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'help' => 'Entrez votre numéro de téléphone (8 chiffres).',
                'attr' => [
                    'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md',
                    'placeholder' => 'Entrez votre téléphone',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 8,
                        'max' => 8,
                        'exactMessage' => 'Le numéro de téléphone doit comporter exactement {{ limit }} chiffres',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+$/',
                        'message' => 'Le numéro de téléphone ne doit contenir que des chiffres.',
                    ]),
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'help' => 'Votre mot de passe doit comporter au moins 6 caractères.',
                    'attr' => [
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md',
                        'placeholder' => 'Entrez un mot de passe',
                    ],
                ],
                'second_options' => [
                    'label' => 'Vérification du mot de passe',
                    'attr' => [
                        'class' => 'w-full px-3 py-2 border border-gray-300 rounded-md',
                        'placeholder' => 'Confirmez le mot de passe',
                    ],
                ],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'mapped' => false,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le mot de passe est obligatoire.',
                    ]),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J\'accepte les termes et conditions',
                'mapped' => false, // This field should not be mapped to the entity
                'constraints' => [
                    new Assert\IsTrue([
                        'message' => 'Vous devez accepter les termes et conditions.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
