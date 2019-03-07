<?php

namespace AppBundle\Form;

use AppBundle\Entity\Book;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        //$coverTransformer = new CoverToEntityTransformer($options['em'], $options['container']);
        //$fileTransformer = new FileToEntityTransformer($options['em'], $options['container']);

        $builder
            ->add('name', TextType::class, ['label' => 'Название'])
            ->add('author', TextType::class, ['label' => 'Автор'])
            ->add('cover', CoverType::class, [
                'label' => false,
            ])
            ->add('file', FileBookType::class, [
                'label' => false,
            ])
            ->add('dateOfReading', DateTimeType::class, ['label' => 'Дата прочтения'])
            ->add('allowDownloading', CheckboxType::class, ['required' => false, 'label' => 'Доступно для скачивания'])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success pull-right',
                ],
                'label' => 'Отправить',
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
                'isEdit' => false,
            ]);
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_book';
    }
}
