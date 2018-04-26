<?php

namespace AppBundle\Controller;

use AppBundle\Entity\File;
use http\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    /**
     * @Route("/action/uploadFile")
     * @Method("POST")
     */
    public function uploadFileAction(Request $request)
    {
        try {
            $file = $request->files->get('my_file');
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $original_name = $file->getClientOriginalName();
            $file->move($this->container->getParameter('file_directory'), $fileName);
            $file_entity = new File();
            $file_entity->setFileName($fileName);
            $file_entity->setActualName($original_name);
            $file_entity->setCreationTime(new \DateTime());

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($file_entity);
            $manager->flush();
            $array = array(
                'status' => 1,
                'file_id' => $file_entity->getId()
            );
            $response = new JsonResponse($array, 200);
            return $response;
        } catch (Exception $e) {
            $array = array('status' => 0);
            $response = new JsonResponse($array, 400);
            return $response;
        }
    }

    /**
     * @Route("/action/downloadFile/{id}")
     */
    public function downloadFileAction($id)
    {
        try {
            $file = $this->getDoctrine()->getRepository('AppBundle:UploadedFile')->find($id);
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
