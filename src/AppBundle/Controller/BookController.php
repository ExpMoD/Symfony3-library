<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
        return $this->render('library/forms/addBook.html.twig');
    }


    /**
     * @Route("/book/edit/{bookId}", name="editBook")
     */

    public function editBookAction($bookId, Request $request)
    {
        return new Response($bookId . ": " . $request->getMethod());
    }
}
