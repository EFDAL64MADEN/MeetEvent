<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Theme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameOfEvent', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Nom de l\'évènement'
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Description de l\'évènement'
            ])
            ->add('startEvent', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-select'],
                'label' => 'Début de l\'évènement'
            ])
            ->add('endEvent', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'form-select'],
                'label' => 'Fin de l\'évènement'
            ])
->add('numberOfPlaces', IntegerType::class, [
    'attr' => ['class' => 'form-control'],
    'label' => 'Nombre de places'
])
            ->add('address', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Adresse'
            ])
            ->add('zipCode', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Code postal'
            ])
            ->add('city', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Ville'
            ])
            ->add('theme', EntityType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Thème',
                'class' => Theme::class
            ])
            ->add('pictures', FileType::class, [
                'label' => 'Ajouter des images',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control']
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
            'data_class' => Event::class,
        ]);
    }
}
