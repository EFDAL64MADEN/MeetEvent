<?php

namespace App\Form;

use App\Entity\Theme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ThemeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('themeName', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Intitulé du thème'
            ])
            ->add('logoFile', FileType::class, [
                'attr' => ['class' => 'form-control', 'id' => 'formFile'],
                'mapped' => false,
                'required' => false,
                'label' => 'Logo'
            ])
            ->add('color', ColorType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Couleur',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary confirm'],
                'label' => 'Valider'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Theme::class, 
        ]);
    }
}