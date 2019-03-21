<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\EventSubscriber\FileRemovingSubscriber;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileRemovingSubscriberTest extends WebTestCase
{
    /**
     * @var int $bookId
     */
    private $bookId = 26;

    /**
     * @var ContainerInterface $container
     */
    private $container;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->container = $this->bootKernel()->getContainer();
    }

    public function testBookAdd()
    {
        $manager = $this->container->get('doctrine');

        /**
         * @var Book $entityBook
         */
        $entityBook = $manager->getRepository('AppBundle:Book')->find($this->bookId);

        $this->assertTrue(!empty($entityBook), 'Нет книги с данным ID');

        $coverEntity = $entityBook->getCover();
        $fileEntity = $entityBook->getFile();

        $fileSystem = new Filesystem();

        $sPublicDir = $this->container->getParameter('public_directory');
        $coverDir = $this->container->getParameter('cover_path');
        $fileDir = $this->container->getParameter('file_path');

        $fileRemoving = new FileRemovingSubscriber($this->container);

        if (!empty($coverEntity)) {
            $fileRemoving->removeCover($coverEntity);

            $coverPath = $sPublicDir . $coverDir . $coverEntity->getPath();
            $this->assertTrue(!$fileSystem->exists($coverPath), 'Обложка существует');
        }

        if (!empty($fileEntity)) {
            $fileRemoving->removeFile($fileEntity);

            $filePath = $sPublicDir . $fileDir . $fileEntity->getPath();
            $this->assertTrue(!$fileSystem->exists($filePath), 'Файл существует');
        }
    }
}
