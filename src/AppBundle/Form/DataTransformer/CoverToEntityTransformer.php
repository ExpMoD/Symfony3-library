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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

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
     * @var RequestStack
     */
    private $request;

    /**
     * @param ContainerInterface $container
     * @param CoverHandler $coverHandler
     * @param RequestStack $request
     */
    public function __construct(ContainerInterface $container, CoverHandler $coverHandler, RequestStack $request)
    {
        $this->container = $container;
        $this->coverHandler = $coverHandler;
        $this->request = $request->getCurrentRequest();
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

        $uploadedCover->setFile($oUploadedFile);

        return $uploadedCover;
    }
}
