<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @Route("/book/action/add", name="addBookAction")
     * @Method("POST")
     */

    public function addBookAction()
    {
        return new Response("Okey");
    }


    /**
     * @Route("/book/form/add", name="addBookForm")
     */

    public function addBookForm()
    {
        return new Response("Okey");
    }


    /**
     * @Route("/book/action/edit", name="editBookAction")
     * @Method("POST")
     */

    public function editBookAction()
    {
        return new Response("Okey");
    }


    /**
     * @Route("/book/form/edit/{bookId}", name="editBookForm")
     */

    public function editBookForm($bookId)
    {
        return new Response($bookId);
    }
}
