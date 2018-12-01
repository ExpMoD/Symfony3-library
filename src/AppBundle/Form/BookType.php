<?php

namespace AppBundle\Form;

use AppBundle\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('author', TextType::class)
            ->add(
                'cover',
                FileType::class,
                array(
                    'required' => true,
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                'image/png',
                                'image/jpg',
                                'image/jpeg'
                            ]
                        ])
                    ]
                )
            )
            ->add(
                'file',
                FileType::class,
                array(
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '5M',
                            'mimeTypes' => [
                                'application/pdf',
                                'application/x-pdf',
                                'application/pdf'
                            ]
                        ])
                    ]
                )
            )
            ->add('dateOfReading', DateTimeType::class)
            ->add('allowDownloading', CheckboxType::class, ['required' => false])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success pull-right'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class
        ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_book';
    }
}
