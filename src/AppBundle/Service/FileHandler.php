<?php

namespace AppBundle\Service;

use AppBundle\Entity\File;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use http\Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FileHandler
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
     * FileHandler constructor.
     * @param ContainerInterface $container
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ContainerInterface $container, ManagerRegistry $managerRegistry)
    {
        $this->container = $container;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @param mixed $file
     * @return File|bool
     */
    public function upload($file)
    {
        $sFileDir = $this->container->getParameter('file_directory');
        $sRandomDir = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(10))), 0, 10);

        if ($file instanceof UploadedFile) {
            try {
                $sFileName = md5(uniqid()) . '.' . $file->guessExtension();
                $sOriginalName = $file->getClientOriginalName();

                $arFile = $file->move($sFileDir . $sRandomDir, $sFileName);

                if (!empty($arFile)) {
                    $fileEntity = new File();
                    $fileEntity->setPath($sRandomDir . '/' . $sFileName);
                    $fileEntity->setActualName($sOriginalName);

                    return $fileEntity;
                }
            } catch (ORMException $e) {
            }
        }

        return false;
    }

    /**
     * @param int $id
     * @return File|null|object
     */
    public function get(int $id)
    {
        $fileEntity = $this->managerRegistry->getRepository('AppBundle:File')->find($id);

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
                $this->managerRegistry->getManager()->remove($fileEntity);
                $this->managerRegistry->getManager()->flush();
                return true;
            } else {
                return false;
            }
        } catch (ORMException $e) {
            return false;
        }
    }

    /**
     * @param File $entity
     * @return bool
     */
    public function delete(File $entity)
    {
        try {
            if (!!$entity) {
                $this->managerRegistry->getManager()->remove($entity);
                $this->managerRegistry->getManager()->flush();
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
                $array = [
                    'status' => 0,
                    'message' => 'File does not exist',
                ];
                $response = new JsonResponse($array, 200);
                return $response;
            }
            $displayName = $entity->getActualName();
            $fileName = $entity->getFileName();
            $fileWithPath = $this->container->getParameter('file_directory') . "/" . $fileName;
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
     * @param File $id
     * @return BinaryFileResponse|JsonResponse
     */
    public function downloadPageByEntity(File $entity)
    {
        try {
            $displayName = $entity->getActualName();
            $fileName = $entity->getPath();
            $fileWithPath = $this->container->getParameter('file_directory') . "/" . $fileName;
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
