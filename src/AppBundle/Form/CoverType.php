<?php

namespace AppBundle\Form;

use AppBundle\Entity\Cover;
use AppBundle\Form\DataTransformer\CoverToEntityTransformer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File as FileConstraints;
use Symfony\Component\Validator\Constraints\Image as ImageConstraints;

class CoverType extends AbstractType
{
    /** @var ContainerInterface */
    protected $container;

    /** @var CoverToEntityTransformer */
    protected $transformer;

    /**
     * @param ContainerInterface $container
     * @param CoverToEntityTransformer $coverToEntityTransformer
     */
    public function __construct(ContainerInterface $container, CoverToEntityTransformer $coverToEntityTransformer)
    {
        $this->container = $container;
        $this->transformer = $coverToEntityTransformer;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('path', HiddenType::class)
            ->add('file', FileType::class, [
                'label' => 'Обложка',
                'required' => true,
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
                'data_class' => Cover::class,
                'isEdit' => false,
            ]);
    }
}
