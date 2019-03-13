<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Form\BookType;
use AppBundle\Service\CoverHandler;
use AppBundle\Service\FileHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class BookController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $arBooks = $this->getDoctrine()->getRepository('AppBundle:Book')->findBy([], ['dateOfReading' => 'DESC']);

        return $this->render('library/index.html.twig', [
            'title' => "Библиотека книг",
            'books' => $arBooks,
        ]);
    }

    /**
     * @Route("/book/add", name="addBook")
     * @IsGranted("ROLE_USER")
     */
    public function addBookAction(Request $request, FileHandler $fileHandler, CoverHandler $coverHandler)
    {
        $manager = $this->getDoctrine()->getManager();
        $bookEntity = new Book();

        $form = $this->createForm(
            BookType::class,
            $bookEntity
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($coverEntity = $coverHandler->upload($form->get('upload_cover')->getData())) {
                $bookEntity->setCover($coverEntity);
            }

            if ($fileEntity = $fileHandler->upload($form->get('upload_file')->getData())) {
                $bookEntity->setFile($fileEntity);
            }

            $manager->persist($bookEntity);
            $manager->flush();

            return $this->redirectToRoute('addBook');
        }
        return $this->render('library/forms/addBook.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/book/edit/{bookId}", name="editBook")
     * @IsGranted("ROLE_USER")
     */
    public function editBookAction($bookId, Request $request, FileHandler $fileHandler, CoverHandler $coverHandler)
    {
        $manager = $this->getDoctrine()->getManager();

        $bookEntity = $manager->getRepository('AppBundle:Book')->findOneBy(['id' => $bookId]);

        if (empty($bookEntity)) {
            $this->addFlash('error', 'Страницы не существует');
            return $this->redirectToRoute('index');
        }

        $form = $this->createForm(
            BookType::class,
            $bookEntity,
            [
                'isEdit' => true,
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedCover = $form->get('upload_cover')->getData();
            $uploadedFile = $form->get('upload_file')->getData();

            if ($form->has('delete_cover')) {
                $deleteCover = $form->get('delete_cover')->getData();
            }
            if ($form->has('delete_file')) {
                $deleteFile = $form->get('delete_file')->getData();
            }

            if ((($uploadedCover instanceof UploadedFile) || !empty($deleteCover)) && !empty($bookEntity->getCover())) {
                $manager->remove($bookEntity->getCover());
                $bookEntity->setCover(null);
            }

            if ((($uploadedFile instanceof UploadedFile) || !empty($deleteFile)) && !empty($bookEntity->getFile())) {
                $manager->remove($bookEntity->getFile());
                $bookEntity->setFile(null);
            }

            if ($coverEntity = $coverHandler->upload($uploadedCover)) {
                $bookEntity->setCover($coverEntity);
            }

            if ($fileEntity = $fileHandler->upload($uploadedFile)) {
                $bookEntity->setFile($fileEntity);
            }

            $manager->merge($bookEntity);
            $manager->flush();

            return $this->redirectToRoute('editBook', ['bookId' => $bookId]);
        }


        return $this->render('library/forms/editBook.html.twig', [
            'form' => $form->createView(),
            'book' => $bookEntity,
        ]);
    }

    /**
     * @Route("/book/delete/{bookId}", name="deleteBook")
     * @IsGranted("ROLE_USER")
     */
    public function deleteBookAction($bookId)
    {
        $manager = $this->getDoctrine()->getManager();

        $bookEntity = $manager->getRepository('AppBundle:Book')->findOneBy(['id' => $bookId]);

        if (!empty($bookEntity)) {
            $manager->remove($bookEntity);
            $manager->flush();

            $this->addFlash('success', 'Книга успешно удалена');
        } else {
            $this->addFlash('error', 'Данного события не существует');
        }

        return $this->redirectToRoute('index');
    }
}
