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
use Symfony\Component\Validator\Constraints as Assert;

class BookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', 
                TextType::class,
                [
                    'label' => 'Title',
                    'attr' => ['placeholder' => 'Input Book title...'],
                    'required' => true,
                ]
            )
            ->add('author', 
                TextType::class,
                [
                    'label' => 'Author',
                    'attr' => ['placeholder' => 'Input Book author...'],
                    'required' => true,
            ])
            ->add('description', 
                TextareaType::class,
                [
                    'label' => 'Description',
                    'attr' => ['placeholder' => 'Input Book description...', 'rows' => 4],
                ]
            )
            ->add('price', 
                NumberType::class,
                [
                    'label' => 'Price',
                    'attr' => ['placeholder' => 'Input Book price...'],
               ] 
            )
            ->add('imagePath', 
                FileType::class,
                [
                    'label' => 'Image',
                    'attr' => ['placeholder' => 'Input Book Cover...', 'accept' => 'image/*'],
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new Assert\Image(['maxSize' => '2M'])
                    ]
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
