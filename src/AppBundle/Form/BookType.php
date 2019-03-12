<?php

namespace AppBundle\Form;

use AppBundle\Entity\Book;
use AppBundle\Entity\Cover;
use AppBundle\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image as ImageConstraints;

class BookType extends AbstractType
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var Book $bookEntity
         */
        $bookEntity = $builder->getData();


        $builder
            ->add('name', TextType::class, ['label' => 'Название'])
            ->add('author', TextType::class, ['label' => 'Автор'])
            ->add('upload_cover', FileType::class, [
                'label' => 'Обложка',
                'mapped' => false,
                'required' => !$options['isEdit'],
                'constraints' => [
                    new ImageConstraints([
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                        ],
                    ]),
                ],
            ])
            ->add('upload_file', FileType::class, [
                'label' => 'Файл',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new ImageConstraints([
                        'mimeTypes' => [
                            'application/pdf',
                        ],
                        'maxSize' => '5Mi',
                    ]),
                ],
            ])
            ->add('dateOfReading', DateTimeType::class, ['label' => 'Дата прочтения'])
            ->add('allowDownloading', CheckboxType::class, ['required' => false, 'label' => 'Доступно для скачивания'])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success pull-right',
                ],
                'label' => 'Отправить',
            ]);


        if (!empty($bookEntity)) {
            if ($bookEntity->getCover() instanceof Cover) {
                $builder->add('cover', CoverBookType::class, [
                    'label' => false,
                ]);
            }

            if ($bookEntity->getFile() instanceof File) {
                $builder->add('file', FileBookType::class, [
                    'label' => false,
                ]);
            }
        }
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => Book::class,
                'isEdit' => false,
            ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_book';
    }
}
