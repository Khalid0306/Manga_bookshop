<?php

namespace App\Form;

use App\Entity\Manga;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nom du tag'
            ])
            ->add('mangas', EntityType::class, [
                'class' => Manga::class,
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'label' => 'Mangas associés'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }
}