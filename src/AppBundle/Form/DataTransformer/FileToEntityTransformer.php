<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 17.05.18
 * Time: 12:08
 */

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\File;
use AppBundle\Service\FileHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileToEntityTransformer implements DataTransformerInterface
{
    /**
     * @var FileHandler
     */
    private $fileHandler;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     * @param FileHandler $fileHandler
     */
    public function __construct(ContainerInterface $container, FileHandler $fileHandler)
    {
        $this->container = $container;
        $this->fileHandler = $fileHandler;
    }

    /**
     * @param  File|string|null $file
     * @return File
     */
    public function transform($file)
    {
        return $file;
    }

    /**
     * @param  File|null $uploadedFile
     * @return File|null
     */
    public function reverseTransform($uploadedFile)
    {
        $oUploadedFile = $uploadedFile->getFile();
        if ($oUploadedFile instanceof UploadedFile) {
            $uploadedFile = $this->fileHandler->upload($oUploadedFile);
        } elseif ($iFileId = $uploadedFile->getId()) {
            $uploadedFile = $this->fileHandler->get($uploadedFile->getId());
        }

        return $uploadedFile;
    }
}
