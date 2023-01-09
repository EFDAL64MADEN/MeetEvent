<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Message;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Il faut ajouter l'option 'id' dans le MessageType pour récupérer l'id de l'envoyeur lors de la création du formulaire dans le Controller
        $id = $options['id'];
        $builder
            ->add('content', TextareaType::class, [
                "attr" => [
                    "class" => "form-control"
                ]
            ])
            ->add('receiver', EntityType::class, [
                "class" => User::class,
                "choice_label" => "nickname",
                "attr" => [
                    "class" => "form-control"
                ],
                // On appelle avec l'option query_builder une fonction qui prend comme paramètre le UserRepository
                // On rajoute l'élément use qui prendra en paramètre l'id du user en session
                "query_builder" => function (UserRepository $ur) use($id){
                    // On retourn la requête DQL qui permet de trouver les followers du user en session
                    return $ur->findFollowers($id);                    
                }
            ])
            ->add('Envoyer', SubmitType::class, [
                "attr" => [
                    "class" => "btn btn-primary"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
        // On spécifie au formulaire que l'option 'id' est obligatoire au moment de la création du formulaire
        $resolver->setRequired('id');
    }
}
