<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 17.05.18
 * Time: 12:08
 */

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Book;
use AppBundle\Entity\Cover;
use AppBundle\Entity\File;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileToEntityTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ObjectManager $om
     * @param ContainerInterface $container
     */
    public function __construct(ObjectManager $om, ContainerInterface $container)
    {
        $this->om = $om;
        $this->container = $container;
    }

    /**
     * @param  File|null $cover
     * @return UploadedFile
     */
    public function transform($cover)
    {
        $uploadFile = false;
        if ($cover instanceof File) {
            if ($cover->getFileName()) {
                $filePath = $this->container->getParameter('file_directory') . "/" . $cover->getFileName();
                $uploadFile = new UploadedFile($filePath, $cover->getActualName());
            }
        } else {
            return null;
        }

        return $uploadFile;
    }

    /**
     * @param  UploadedFile|null $uploadFile
     * @return File|null
     */
    public function reverseTransform($uploadFile)
    {
        if (is_null($uploadFile)) {
            return null;
        }

        $entity = null;
        $entity = $this->om->getRepository('AppBundle:File')->findOneBy(['fileName' => $uploadFile->getBasename()]);

        if (!$entity) {
            $fileName = md5(uniqid()) . '.' . $uploadFile->guessExtension();
            $originalName = $uploadFile->getClientOriginalName();
            $uploadFile->move($this->container->getParameter('file_directory'), $fileName);
            $entity = new Cover();
            $entity->setFileName($fileName);
            $entity->setActualName($originalName);
            //$this->om->persist($entity);
        }

        return $entity;
    }
}
