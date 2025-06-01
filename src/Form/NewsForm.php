<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\News;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'News Title',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter news title',
                ],
            ])
            ->add('description', null, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Enter a brief description',
                ],
            ])
            ->add('content', null, [
                'label' => 'Content',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Enter the full content of the news',
                ],
            ])

            ->add('picture', FileType::class, [
                
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'accept' => 'image/*',
                ],
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
