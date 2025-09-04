<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Tag;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('status')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
            ])
            ->add('parent', EntityType::class, [
        'class' => Task::class,
        'choice_label' => 'title',
        'required' => false,
        'placeholder' => 'Ninguna (tarea principal)',
    ])
          ->add('tagNames', TextType::class, [
                'label' => 'Tags',
                'mapped' => false, // No se mapea directamente a la entidad
                'required' => false,
                'help' => 'Escribe los tags separados por comas',
                'attr' => [
                    'placeholder' => 'trabajo, urgente, personal...'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
