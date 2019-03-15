<?php

namespace AppBundle\Service;

use AppBundle\Entity\Cover;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\ORMException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * CoverHandler constructor.
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
     * @return Cover|bool
     */
    public function upload($file)
    {
        $sPublicDir = $this->container->getParameter('public_directory');
        $sCoverDir = $this->container->getParameter('cover_path');
        $sRandomDir = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(10))), 0, 10);

        if ($file instanceof UploadedFile) {
            try {
                $sFileName = md5(uniqid()) . '.' . $file->guessExtension();
                $sOriginalName = $file->getClientOriginalName();

                $arFile = $file->move($sPublicDir . $sCoverDir . $sRandomDir, $sFileName);

                if (!empty($arFile)) {
                    $coverEntity = new Cover();
                    $coverEntity->setPath($sRandomDir . '/' . $sFileName);
                    $coverEntity->setActualName($sOriginalName);

                    return $coverEntity;
                }
            } catch (ORMException $e) {
            }
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
                $this->managerRegistry->getManager()->remove($fileEntity);
                $this->managerRegistry->getManager()->flush();
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
                $this->managerRegistry->getManager()->remove($entity);
                $this->managerRegistry->getManager()->flush();
                return true;
            } else {
                return false;
            }
        } catch (ORMException $e) {
        }

        return false;
    }
}
