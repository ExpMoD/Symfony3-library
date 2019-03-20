<?php

namespace AppBundle\EventSubscriber;

use AppBundle\Entity\Cover;
use AppBundle\Entity\File;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileRemovingSubscriber implements EventSubscriber
{
    /**
     * @var ContainerInterface $container
     */
    public $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::preRemove,
        ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Cover) {
            $this->removeCover($entity);
        } elseif ($entity instanceof File) {
            $this->removeFile($entity);
        }
    }

    public function removeCover(Cover $entity)
    {
        $sPublicDir = $this->container->getParameter('public_directory');
        $sCoverDir = $this->container->getParameter('cover_path');

        $sCoverPath = $sPublicDir . $sCoverDir . $entity->getPath();

        $fs = new Filesystem();

        if (!empty($entity->getPath()) && $fs->exists($sCoverPath)) {
            $fs->remove($sCoverPath);
        }
    }

    public function removeFile(File $entity)
    {
        $sPublicDir = $this->container->getParameter('public_directory');
        $sFileDir = $this->container->getParameter('file_path');

        $sFilePath = $sPublicDir . $sFileDir . $entity->getPath();

        $fs = new Filesystem();

        if (!empty($entity->getPath()) && $fs->exists($sFilePath)) {
            $fs->remove($sFilePath);
        }
    }
}
