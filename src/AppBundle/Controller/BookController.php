<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Entity\File;
use AppBundle\Entity\Image;
use AppBundle\Service\FileHandler;
use AppBundle\Service\ImageHandler;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('library/index.html.twig', [
            'title' => "Библиотека книг",
            'books' => [
                [
                    "ID" => 1,
                    "NAME" => "One day",
                    "AUTHOR" => "J.J. Abee",
                    "COVER" => "/covers/jpg1.png",
                    "URL" => "/download/book/book1.pdf",
                    "ALLOW_DOWNLOADING" => false
                ],
                [
                    "ID" => 2,
                    "NAME" => "Two day",
                    "AUTHOR" => "J.J. Abee",
                    "COVER" => "/covers/jpg2.png",
                    "URL" => "/download/book/book2.pdf",
                    "ALLOW_DOWNLOADING" => true
                ],
            ]
        ]);
    }

    /**
     * @Route("/book/add", name="addBook")
     */
    public function addBookAction(Request $request)
    {
        $paramsArray = [];
        try {
            if ($request->getMethod() == 'POST') {
                $fileController = new FileHandler($this->container, $this->getDoctrine());
                $imageController = new ImageHandler($this->container, $this->getDoctrine());

                $uploadedFile = $request->files->get('file');
                $uploadedImage = $request->files->get('cover');

                if (get_class($uploadedFile) == UploadedFile::class) {
                    $fileEntity = $fileController->upload($uploadedFile);
                }

                if (get_class($uploadedImage) == UploadedFile::class) {
                    $imageEntity = $fileController->upload($uploadedImage);
                }
                //var_dump($file->getId());
            }
        } catch (ORMException $e) {
        }

        return $this->render('library/forms/addBook.html.twig');
    }

    /**
     * @Route("/book/edit/{bookId}", name="editBook")
     */
    public function editBookAction($bookId, Request $request)
    {
        /*$bookEntity = new Book();
        $fileEntity = new File();
        $imageEntity = new Image();

        $fileEntity->setFileName('f1');
        $fileEntity->setActualName('f11');
        $fileEntity->setCreationTime(new \DateTime());


        $imageEntity->setFileName('i1');
        $imageEntity->setActualName('i11');
        $imageEntity->setCreationTime(new \DateTime());

        $bookEntity->setName('Okey');
        $bookEntity->setAuthor('Olo');
        $bookEntity->setAllowDownloading(true);
        $bookEntity->setDateOfReading(new \DateTime());
        $bookEntity->setFile($fileEntity);
        $bookEntity->setCover($imageEntity);


        $manager = $this->getDoctrine()->getManager();
        $manager->persist($fileEntity);
        $manager->persist($imageEntity);
        $manager->persist($bookEntity);
        $manager->flush();*/

        /*$manager = $this->getDoctrine()->getManager();

        $book = $manager->getRepository('AppBundle:Book')->find(2);

        $fileController = $this->get('file_handler_service');

        $fileController->uploadFileAction($request->files->get('file'));*/


        return new Response(/*$book->getCover()->getFileName()*/);
    }
}
