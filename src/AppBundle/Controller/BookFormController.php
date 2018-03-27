<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookFormController extends Controller
{
    /**
     * @Route("/book/form/add")
     */
    public function add()
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
     * @Route("/book/form/edit/{bookId}")
     */

    public function edit($bookId)
    {
    }
}
