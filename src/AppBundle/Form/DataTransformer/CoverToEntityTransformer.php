<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 17.05.18
 * Time: 12:08
 */

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Cover;
use AppBundle\Service\CoverHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CoverToEntityTransformer implements DataTransformerInterface
{
    /**
     * @var CoverHandler
     */
    private $coverHandler;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     * @param CoverHandler $coverHandler
     */
    public function __construct(ContainerInterface $container, CoverHandler $coverHandler)
    {
        $this->container = $container;
        $this->coverHandler = $coverHandler;
    }

    /**
     * @param  Cover|string|null $cover
     * @return Cover
     */
    public function transform($cover)
    {
        return $cover;
    }

    /**
     * @param  Cover|null $uploadedCover
     * @return Cover|null
     */
    public function reverseTransform($uploadedCover)
    {
        $oUploadedFile = $uploadedCover->getFile();
        if ($oUploadedFile instanceof UploadedFile) {
            $uploadedCover = $this->coverHandler->upload($oUploadedFile);
        } elseif ($iCoverId = $uploadedCover->getId()) {
            $uploadedCover = $this->coverHandler->get($iCoverId);
        }

        return $uploadedCover;
    }
}
