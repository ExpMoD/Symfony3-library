<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Form\BookType;
use AppBundle\Repository\BookRepository;
use AppBundle\Service\CoverHandler;
use AppBundle\Service\FileHandler;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function index(
        Request $request,
        BookRepository $bookRepository,
        TagAwareAdapter $cache,
        Paginator $paginator
    ) {
        $page = $request->query->getInt('page', 1);
        $itemPerPage = $this->getParameter('items_per_page');

        $cacheItem = $cache->getItem('book_list_page_' . $page);

        if ($cacheItem->isHit()) {
            $arBooks = $cacheItem->get();
        } else {
            $arBooks = $paginator->paginate(
                $bookRepository->getAllBooks(),
                $page,
                $itemPerPage
            );

            $cacheItem->set($arBooks);
            $cacheItem->tag($this->getParameter('book_list_cache_key'));
            $cache->save($cacheItem);
        }

        return $this->render('library/index.html.twig', [
            'title' => 'Библиотека книг',
            'subtitle' => 'Список книг',
            'books' => $arBooks,
        ]);
    }

    /**
     * @Route("/book/add", name="addBook")
     * @IsGranted("ROLE_USER")
     */
    public function addBookAction(
        Request $request,
        FileHandler $fileHandler,
        CoverHandler $coverHandler,
        TagAwareAdapter $cache
    ) {
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

            $cache->invalidateTags([$this->getParameter('book_list_cache_key')]);

            $this->addFlash('success', 'Книга успешно добавлена');
            return $this->redirectToRoute('index');
        }
        return $this->render('library/forms/addBook.html.twig', [
            'title' => 'Добавление книги',
            'subtitle' => 'Добавление книги',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/book/edit/{bookId}", name="editBook")
     * @IsGranted("ROLE_USER")
     */
    public function editBookAction(
        $bookId,
        Request $request,
        FileHandler $fileHandler,
        CoverHandler $coverHandler,
        TagAwareAdapter $cache
    ) {
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

            $cache->invalidateTags([$this->getParameter('book_list_cache_key')]);

            $this->addFlash('success', 'Книга успешно обновлена');
            return $this->redirectToRoute('index');
        }


        return $this->render('library/forms/editBook.html.twig', [
            'title' => 'Редактирование книги',
            'subtitle' => 'Редактирование книги',
            'form' => $form->createView(),
            'book' => $bookEntity,
        ]);
    }

    /**
     * @Route("/book/delete/{bookId}", name="deleteBook")
     * @IsGranted("ROLE_USER")
     */
    public function deleteBookAction($bookId, TagAwareAdapter $cache)
    {
        $manager = $this->getDoctrine()->getManager();

        $bookEntity = $manager->getRepository('AppBundle:Book')->findOneBy(['id' => $bookId]);

        if (!empty($bookEntity)) {
            $manager->remove($bookEntity);
            $manager->flush();

            $cache->invalidateTags([$this->getParameter('book_list_cache_key')]);

            $this->addFlash('success', 'Книга успешно удалена');
        } else {
            $this->addFlash('error', 'Данного события не существует');
        }

        return $this->redirectToRoute('index');
    }
}
