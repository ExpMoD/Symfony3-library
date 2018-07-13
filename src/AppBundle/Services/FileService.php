<?php

namespace AppBundle\Services;

use AppBundle\Entity\File;
use http\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FileService
{
    /**
     * @var string
     */
    private $fileDirectory;

    public function __construct(ParameterBagInterface $params)
    {
        $this->fileDirectory = $params->get('file_directory');
    }

    public function uploadFileAction($file)
    {
        try {
            var_dump($this->fileDirectory);
            /*
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $original_name = $file->getClientOriginalName();
            $file->move($this->fileDirectory, $fileName);
            $fileEntity = new File();
            $fileEntity->setFileName($fileName);
            $fileEntity->setActualName($original_name);
            $fileEntity->setCreationTime(new \DateTime());

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($fileEntity);
            $manager->flush();
            return $fileEntity;*/
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @Route("/action/downloadFile/{id}")
     */
    public function downloadFileAction($id)
    {
        try {
            $file = $this->getDoctrine()->getRepository('AppBundle:File')->find($id);
            if (!$file) {
                $array = array(
                    'status' => 0,
                    'message' => 'File does not exist'
                );
                $response = new JsonResponse($array, 200);
                return $response;
            }
            $displayName = $file->getActualName();
            $fileName = $file->getFileName();
            $file_with_path = $this->container->getParameter('file_directory') . "/" . $fileName;
            $response = new BinaryFileResponse($file_with_path);
            $response->headers->set('Content-Type', 'text/plain');
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $displayName);
            return $response;
        } catch (Exception $e) {
            $array = array(
                'status' => 0,
                'message' => 'Download error'
            );
            $response = new JsonResponse($array, 400);
            return $response;
        }
    }

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
