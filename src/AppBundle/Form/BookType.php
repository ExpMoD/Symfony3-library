<?php

namespace AppBundle\Form;

use AppBundle\Entity\Book;
use AppBundle\Form\DataTransformer\CoverToEntityTransformer;
use AppBundle\Form\DataTransformer\FileToEntityTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $coverTransformer = new CoverToEntityTransformer($options['em'], $options['container']);
        $fileTransformer = new FileToEntityTransformer($options['em'], $options['container']);

        $builder
            ->add('name', TextType::class)
            ->add('author', TextType::class)
            ->add(
                $builder
                    ->create(
                        'cover',
                        'Symfony\Component\Form\Extension\Core\Type\FileType',
                        array(
                            'required' => !!$options['isEdit'],
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
                    ->addModelTransformer($coverTransformer)
            )
            ->add(
                $builder
                    ->create(
                        'file',
                        'Symfony\Component\Form\Extension\Core\Type\FileType',
                        array(
                            'required' => false,
                            'constraints' => [
                                new File([
                                    'maxSize' => '5M',
                                    'mimeTypes' => [
                                        'application/pdf',
                                        'application/x-pdf'
                                    ]
                                ])
                            ]
                        )
                    )
                    ->addModelTransformer($fileTransformer)
            )
            ->add('dateOfReading', DateTimeType::class)
            ->add('allowDownloading', CheckboxType::class, ['required' => false])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success pull-right'
                ]
            ]);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Book::class,
                'isEdit' => false
            ])
            ->setRequired(['em', 'container'])
            ->setAllowedTypes('em', 'Doctrine\Common\Persistence\ObjectManager')
            ->setAllowedTypes('container', 'Symfony\Component\DependencyInjection\ContainerInterface')
        ;
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_book';
    }
}
