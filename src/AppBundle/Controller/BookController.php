<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Form\BookType;
use AppBundle\Service\CoverHandler;
use AppBundle\Service\FileHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class BookController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function index(int $page = 1)
    {
        $coverDir = $this->container->getParameter('cover_path');
        $fileDir = $this->container->getParameter('file_path');

        $rsBooks = $this->getDoctrine()->getRepository('AppBundle:Book')->findBy([], ['dateOfReading' => 'DESC']);

        $arBooks = [];
        foreach ($rsBooks as $book) {
            $arBooks[] = [
                "ID" => $book->getId(),
                "NAME" => $book->getName(),
                "AUTHOR" => $book->getAuthor(),
                "COVER" => ($book->getCover()) ? $coverDir . "/" . $book->getCover()->getFileName() : false,
                "FILE" => $this->generateUrl('downloadBook', ['bookId' => $book->getId()]),
                "ALLOW_DOWNLOADING" => $book->getAllowDownloading(),
            ];
        }

        return $this->render('library/index.html.twig', [
            'title' => "Библиотека книг",
            'books' => $arBooks,
        ]);
    }

    /**
     * @Route("/book/add", name="addBook")
     */
    public function addBookAction(Request $request)
    {
        $paramsArray = [];

        $bookEntity = new Book();

        $form = $this->createForm(
            BookType::class,
            $bookEntity,
            [
                'em' => $this->getDoctrine()->getManager(),
                'container' => $this->container,
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $fileController = new FileHandler($this->container, $this->getDoctrine());
            $coverController = new CoverHandler($this->container, $this->getDoctrine());

            $uploadedCover = $request->files->get('app_bundle_book')['cover'];
            $uploadedFile = $request->files->get('app_bundle_book')['file'];

            $uploadedCover = $uploadedCover ? $uploadedCover : false;

            $uploadedFile = $uploadedFile ? $uploadedFile : false;

            $coverEntity = false;
            if ($uploadedCover && get_class($uploadedCover) == UploadedFile::class) {
                $coverEntity = $coverController->upload($uploadedCover);
            }

            $fileEntity = false;
            if ($uploadedFile && get_class($uploadedFile) == UploadedFile::class) {
                $fileEntity = $fileController->upload($uploadedFile);
            }

            if (!!$coverEntity) {
                $bookEntity->setCover($coverEntity);
            }

            if (!!$fileEntity) {
                $bookEntity->setFile($fileEntity);
            }

            $this->getDoctrine()->getManager()->persist($bookEntity);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('addBook');
        }
        return $this->render('library/forms/addBook.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/book/edit/{bookId}", name="editBook")
     */
    public function editBookAction($bookId, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $bookEntity = $manager->getRepository('AppBundle:Book')->findOneBy(['id' => $bookId]);

        //$bookEntity = new Book();

        //$bookEntity->setCover((new Cover()));


        $form = $this->createForm(
            BookType::class,
            $bookEntity,
            [
                'em' => $this->getDoctrine()->getManager(),
                'container' => $this->container,
                'isEdit' => true,
            ]
        );

        $data = $form->getData();
        throw new \Exception(var_dump($data->getCover()));

        if ($form->isSubmitted() && $form->isValid()) {
            //$this->getDoctrine()->getManager()->persist($bookEntity);
            //$this->getDoctrine()->getManager()->flush();
        }


        return $this->render('library/forms/addBook.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/book/download/{bookId}", name="downloadBook")
     */
    public function downloadBookAction($bookId)
    {
        $fileController = new FileHandler($this->container, $this->getDoctrine());

        $manager = $this->getDoctrine()->getManager();

        $book = $manager->getRepository('AppBundle:Book')->find($bookId);

        if ($book->getAllowDownloading()) {
            return ($book->getFile()) ? $fileController->downloadPageByEntity($book->getFile()) : false;
        } else {
            return false;
        }
    }
}
