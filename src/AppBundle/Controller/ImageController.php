<?php

namespace AppBundle\Controller;

use AppBundle\Entity\File;
use http\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ImageController extends Controller
{
    /**
     * @Route("/action/uploadImage")
     */
    public function uploadImageAction()
    {
        return new Response("Okey");
    }

    /**
     * @Route("/action/getImage/{id}")
     */
    public function getImageAction($id)
    {
        return new Response($this->container->getParameter('file_directory'));
    }
}
