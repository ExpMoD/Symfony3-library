<?php

namespace AppBundle\Service;

use AppBundle\Entity\Cover;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use http\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
     * CoverHandler constructor.
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
        $sCoverDir = $this->container->getParameter('cover_directory');
        $sRandomDir = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(10))), 0, 10);

        try {
            $sFileName = md5(uniqid()) . '.' . $file->guessExtension();
            $sOriginalName = $file->getClientOriginalName();

            $arFile = $file->move($sCoverDir . $sRandomDir, $sFileName);

            if (!empty($arFile)) {
                $coverEntity = new Cover();
                $coverEntity->setPath($sRandomDir . '/' . $sFileName);
                $coverEntity->setActualName($sOriginalName);

                return $coverEntity;
            }
        } catch (ORMException $e) {
        }

        return false;
    }

    /**
     * @param int $id
     * @return Cover|null|object
     */
    public function get(int $id)
    {
        $fileEntity = $this->managerRegistry->getRepository('AppBundle:Cover')->find($id);

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
        }

        return false;
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
        }

        return false;
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
                $array = [
                    'status' => 0,
                    'message' => 'File does not exist',
                ];
                $response = new JsonResponse($array, 200);
                return $response;
            }
            $displayName = $entity->getActualName();
            $fileName = $entity->getPath();
            $fileWithPath = $this->container->getParameter('cover_directory') . "/" . $fileName;
            $response = new BinaryFileResponse($fileWithPath);
            $response->headers->set('Content-Type', 'text/plain');
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $displayName);
            return $response;
        } catch (Exception $e) {
            $array = [
                'status' => 0,
                'message' => 'Download error',
            ];
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
            $fileName = $entity->getPath();
            $fileWithPath = $this->container->getParameter('cover_directory') . "/" . $fileName;
            $response = new BinaryFileResponse($fileWithPath);
            $response->headers->set('Content-Type', 'text/plain');
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $displayName);
            return $response;
        } catch (Exception $e) {
            $array = [
                'status' => 0,
                'message' => 'Download error',
            ];
            $response = new JsonResponse($array, 400);
            return $response;
        }
    }
}
