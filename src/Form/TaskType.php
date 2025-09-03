<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
                'choice_label' => 'id',
            ])
            ->add('parent', EntityType::class, [
        'class' => Task::class,
        'choice_label' => 'title',
        'required' => false,
        'placeholder' => 'Ninguna (tarea principal)',
    ])
          ->add('tags', EntityType::class, [
        'class' => Tag::class,
        'choice_label' => 'name',
        'multiple' => true,
        'expanded' => true, // puedes poner false si quieres un select en lugar de checkboxes
    ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
