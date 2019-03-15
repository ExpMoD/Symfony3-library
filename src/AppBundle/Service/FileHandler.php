<?php

namespace AppBundle\Service;

use AppBundle\Entity\File;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
}
