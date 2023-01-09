<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use VictorPrdh\RecaptchaBundle\Form\ReCaptchaType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Email',
                    'style' => 'width: 300px; display: flex; flex-direction: column; margin: 0 0 10px 0; padding: 0 0 0 15px'
                ],
                'label' => 'Email'
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'options' => ['attr' => ['class' => 'input']],
                'constraints' => [
                    new Regex('^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$^', 'Le mot de passe doit contenir au moins 8 caractères, dont au moins une majuscule, une minuscule et un chiffre')
                ],
                'required' => true,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'Mot de passe',
                        'class' => 'input',
                        'style' => 'width: 300px; display: flex; flex-direction: column; margin: 0 0 10px 0; padding: 0 0 0 15px'
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmer mot de passe',
                    'attr' => [
                        'placeholder' => 'Confirmer mot de passe',
                        'class' => 'input',
                        'style' => 'width: 300px; display: flex; flex-direction: column; margin: 0 0 10px 0; padding: 0 0 0 15px'
                    ]
                ],
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false
            ])
            ->add('nickname', TextType::class, [
                'attr' => [
                    'class' => 'input',
                    'placeholder' => 'Pseudo',
                    'style' => 'width: 300px; display: flex; flex-direction: column; margin: 0 0 10px 0; padding: 0 0 0 15px'
                ],
                'label' => 'Pseudo'
            ])
            ->add('dateOfBirth', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'input date',
                    'style' => 'width: 300px; display: flex; margin: 0 0 10px 0; padding: 0 15px 0 15px'
                ],
                'label' => 'Date de naissance'
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter nos conditions.',
                    ]),
                ],
                'label' => 'Accepter conditions',
                'attr' => ['style' => 'margin: 15px 5px']
            ])
->add('captcha', Recaptcha3Type::class, [
    'constraints' => new Recaptcha3(),
    'action_name' => 'register'
])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'sign-up-button'],
                'label' => 'Valider',
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