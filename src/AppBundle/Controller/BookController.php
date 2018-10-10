<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Form\BookType;
use AppBundle\Service\FileHandler;
use AppBundle\Service\CoverHandler;
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

        $bookEntity = new Book();

        $form = $this->createForm(BookType::class, $bookEntity);

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
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/book/edit/{bookId}", name="editBook")
     */
    public function editBookAction($bookId, Request $request)
    {
        $manager = $this->getDoctrine()->getManager();

        $book = $manager->getRepository('AppBundle:Book')->find($bookId);


        var_dump($book->getCover()->getFileName());

        return new Response("");
    }
}
