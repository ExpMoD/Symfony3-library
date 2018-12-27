<?php

namespace AppBundle\Service;

use AppBundle\Entity\Cover;
use http\Exception;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CoverHandler
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ObjectRepository
     */
    protected $objectRepository;


    /**
     * FileHandler constructor.
     * @param ContainerInterface $container
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ContainerInterface $container, ManagerRegistry $managerRegistry)
    {
        $this->container = $container;
        $this->managerRegistry = $managerRegistry;
        $this->entityManager = $managerRegistry->getManager();
    }

    /**
     * @param UploadedFile $file
     * @return Cover|bool
     */
    public function upload(UploadedFile $file)
    {
        try {
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $originalName = $file->getClientOriginalName();
            $file->move($this->container->getParameter('cover_directory'), $fileName);
            $entity = new Cover();
            $entity->setFileName($fileName);
            $entity->setActualName($originalName);

            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            return $entity;
        } catch (ORMException $e) {
            return false;
        }
    }

    /**
     * @param int $id
     * @return Cover|null|object
     */
    public function get(int $id)
    {
        $fileEntity = $this->managerRegistry->getRepository('Cover.php')->find($id);

        return $fileEntity;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteById(int $id)
    {
        try {
            $fileEntity = $this->get($id);

            if (!!$fileEntity) {
                $this->entityManager->remove($fileEntity);
                $this->entityManager->flush();
                return true;
            } else {
                return false;
            }
        } catch (ORMException $e) {
            return false;
        }
    }

    /**
     * @param Cover $entity
     * @return bool
     */
    public function delete(Cover $entity)
    {
        try {
            if (!!$entity) {
                $this->entityManager->remove($entity);
                $this->entityManager->flush();
                return true;
            } else {
                return false;
            }
        } catch (ORMException $e) {
            return false;
        }
    }

    /**
     * @param int $id
     * @return BinaryFileResponse|JsonResponse
     */
    public function downloadPageById(int $id)
    {
        try {
            $entity = $this->get($id);
            if (!$entity) {
                $array = array(
                    'status' => 0,
                    'message' => 'File does not exist'
                );
                $response = new JsonResponse($array, 200);
                return $response;
            }
            $displayName = $entity->getActualName();
            $fileName = $entity->getFileName();
            $fileWithPath = $this->container->getParameter('cover_directory') . "/" . $fileName;
            $response = new BinaryFileResponse($fileWithPath);
            $response->headers->set('Content-Type', 'text/plain');
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $displayName);
            return $response;
        } catch (Exception $e) {
            $array = array(
                'status' => 0,
                'message' => 'Download error'
            );
            $response = new JsonResponse($array, 400);
            return $response;
        }
    }

    /**
     * @param Cover $entity
     * @return BinaryFileResponse|JsonResponse
     */
    public function downloadPageByEntity(Cover $entity)
    {
        try {
            $displayName = $entity->getActualName();
            $fileName = $entity->getFileName();
            $fileWithPath = $this->container->getParameter('cover_directory') . "/" . $fileName;
            $response = new BinaryFileResponse($fileWithPath);
            $response->headers->set('Content-Type', 'text/plain');
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $displayName);
            return $response;
        } catch (Exception $e) {
            $array = array(
                'status' => 0,
                'message' => 'Download error'
            );
            $response = new JsonResponse($array, 400);
            return $response;
        }
    }
}
