<?php

namespace AppBundle\Controller\api\v1;

use AppBundle\Entity\Book;
use AppBundle\Repository\BookRepository;
use AppBundle\Service\ResponseApiHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookApiController extends Controller
{
    private function checkApiKey($apiKey)
    {
        return ($apiKey === $this->getParameter('api_key_v1'));
    }

    /**
     * @Route("/book/list")
     */
    public function bookListAction(
        Request $request,
        BookRepository $bookRepository,
        ResponseApiHandler $response,
        TagAwareAdapter $cache
    ) {
        if (!$this->checkApiKey($request->get('api-key'))) {
            $response->setMessage('Invalid api key', Response::HTTP_BAD_REQUEST);
        } else {
            $cacheItem = $cache->getItem('api_book_list');

            if ($cacheItem->isHit()) {
                $arBooks = $cacheItem->get();
            } else {
                $coverPath = $this->getParameter('cover_path');
                $filePath = $this->getParameter('file_path');

                $rsBooks = $bookRepository->getAllBooks()->getResult();

                $arBooks = [];

                /**
                 * @var Book $arBook
                 */
                foreach ($rsBooks as $arBook) {
                    $arBooks[] = [
                        'id' => $arBook->getId(),
                        'name' => $arBook->getName(),
                        'author' => $arBook->getAuthor(),
                        'dateOfReading' => $arBook->getDateOfReading(),
                        'allowDownloading' => $arBook->getAllowDownloading(),
                        'cover' => (!empty($arBook->getCover()))
                            ? $request->getUriForPath($coverPath . $arBook->getCover()->getPath())
                            : null,
                        'file' => (!empty($arBook->getFile()))
                            ? $request->getUriForPath($filePath . $arBook->getFile()->getPath())
                            : null,
                    ];
                }

                $cacheItem->set($arBooks);
                $cacheItem->tag($this->getParameter('book_list_cache_key'));
                $cache->save($cacheItem);
            }

            $response->setData($arBooks);
        }

        return $response->getResponse();
    }

    /**
     * @Route("/book/add")
     */
    public function bookAddAction(Request $request, ResponseApiHandler $response, TagAwareAdapter $cache)
    {
        $manager = $this->getDoctrine()->getManager();

        if (!$this->checkApiKey($request->get('api-key'))) {
            $response->setMessage('Invalid api key', Response::HTTP_BAD_REQUEST);
        } else {
            $sName = $request->get('name');
            $sAuthor = $request->get('author');

            if (empty($sName)) {
                $response->setMessage('Missed required field "name"', Response::HTTP_BAD_REQUEST);
            }

            if (empty($sAuthor)) {
                $response->setMessage('Missed required field "author"', Response::HTTP_BAD_REQUEST);
            }

            if (!$response->existError()) {
                $bookEntity = new Book();

                $bookEntity->setName($sName);
                $bookEntity->setAuthor($sAuthor);

                if ($dateOfReading = $request->get('dateOfReading')) {
                    $bookEntity->setDateOfReading(new \DateTime($dateOfReading));
                }

                if (!(is_null($allowDownloading = $request->get('allowDownloading')))) {
                    $bookEntity->setAllowDownloading(!!$allowDownloading);
                }

                $manager->persist($bookEntity);
                $manager->flush();

                $cache->invalidateTags([$this->getParameter('book_list_cache_key')]);

                $response->setStatusCode(Response::HTTP_OK);
            }
        }

        return $response->getResponse();
    }

    /**
     * @Route("/book/{bookId}/edit", requirements={"bookId"="\d+"})
     */
    public function bookEditAction(int $bookId, Request $request, ResponseApiHandler $response, TagAwareAdapter $cache)
    {
        $manager = $this->getDoctrine()->getManager();

        if (!$this->checkApiKey($request->get('api-key'))) {
            $response->setMessage('Invalid api key', Response::HTTP_BAD_REQUEST);
        } else {
            $bookEntity = $manager->getRepository('AppBundle:Book')->find($bookId);

            if (empty($bookEntity)) {
                $response->setMessage('Book not found', Response::HTTP_NOT_FOUND);
            } else {
                if ($sName = $request->get('name')) {
                    $bookEntity->setName($sName);
                }

                if ($sAuthor = $request->get('author')) {
                    $bookEntity->setAuthor($sAuthor);
                }

                if ($dateOfReading = $request->get('dateOfReading')) {
                    $bookEntity->setDateOfReading(new \DateTime($dateOfReading));
                }

                if (!(is_null($allowDownloading = $request->get('allowDownloading')))) {
                    $bookEntity->setAllowDownloading(!!$allowDownloading);
                }

                $manager->merge($bookEntity);
                $manager->flush();

                $cache->invalidateTags([$this->getParameter('book_list_cache_key')]);

                $response->setStatusCode(Response::HTTP_OK);
            }
        }

        return $response->getResponse();
    }
}
