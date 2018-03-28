<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BookController extends Controller
{
    /**
     * @Route("/")
     */

    public function index()
    {
        return $this->render('library/index.html.twig', [
            'title' => "Библиотека книг",
            'books' => [
                [
                    "NAME" => "One day",
                    "AUTHOR" => "J.J. Abee",
                    "COVER" => "/covers/jpg1.png",
                    "URL" => "/download/book/book1.pdf",
                    "ALLOW_DOWNLOADING" => false],
                ["NAME" => "Two day",
                    "AUTHOR" => "J.J. Abee",
                    "COVER" => "/covers/jpg2.png",
                    "URL" => "/download/book/book2.pdf",
                    "ALLOW_DOWNLOADING" => true],
            ]
        ]);
    }


    /**
     * @Route("/book/action/add")
     * @Method("POST")
     */

    public function addBookAction()
    {
    }


    /**
     * @Route("/book/form/add")
     */

    public function addBookForm()
    {
    }


    /**
     * @Route("/book/action/edit")
     * @Method("POST")
     */

    public function editBookAction()
    {
        return new Response("Okey");
    }


    /**
     * @Route("/book/form/edit/{bookId}")
     */

    public function editBookForm($bookId)
    {
    }
}
