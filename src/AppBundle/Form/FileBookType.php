<?php

namespace AppBundle\Form;

use AppBundle\Entity\Cover;
use AppBundle\Form\DataTransformer\FileToEntityTransformer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class FileBookType extends AbstractType
{
    /** @var ContainerInterface */
    protected $container;

    /** @var FileToEntityTransformer */
    protected $transformer;

    /**
     * @param ContainerInterface $container
     * @param FileToEntityTransformer $fileToEntityTransformer
     */
    public function __construct(ContainerInterface $container, FileToEntityTransformer $fileToEntityTransformer)
    {
        $this->container = $container;
        $this->transformer = $fileToEntityTransformer;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('path', FileType::class, [
                'required' => true,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                        ],
                    ]),
                ],
                'label' => 'Обложка',
            ])
            ->addModelTransformer($this->transformer);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => \AppBundle\Entity\File::class,
                'isEdit' => false,
            ]);
    }
}
