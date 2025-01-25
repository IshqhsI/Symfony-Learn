<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', 
                TextType::class,
                [
                    'label' => 'Title',
                    'attr' => ['placeholder' => 'Book title'],
                    'required' => true,
                    'error_bubbling' => true
                ]
            )
            ->add('author', 
                TextType::class,
                [
                    'label' => 'Author',
                    'attr' => ['placeholder' => 'Book author'],
                    'required' => true,
                    'error_bubbling' => true
            ])
            ->add('description', 
                TextareaType::class,
                [
                    'label' => 'Description',
                    'attr' => ['placeholder' => 'Book description', 'rows' => 3],
                    'error_bubbling' => true
                ]
            )
            ->add('price', 
                NumberType::class,
                [
                    'label' => 'Price',
                    'attr' => ['placeholder' => 'Book price'],
                    'error_bubbling' => true
               ] 
            )
            ->add('imagePath', 
                FileType::class,
                [
                    'label' => 'Image path',
                    'attr' => ['placeholder' => 'Book image path'],
                    'error_bubbling' => true,
                ]
            )
            ->add('submit',
                SubmitType::class,)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
