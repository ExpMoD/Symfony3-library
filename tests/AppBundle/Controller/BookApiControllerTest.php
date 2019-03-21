<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BookApiControllerTest extends WebTestCase
{
    const API_KEY = 'api-key';

    public function testBookAdd()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/v1/book/add',
            [
                'api-key' => self::API_KEY,
                'name' => 'Навание Тест API',
                'author' => 'Автор Тест API',
                'dateOfReading' => '21.07.2018 20:02:15',
            ]
        );

        $response = $client->getResponse();

        $result = json_decode($response->getContent(), true);

        $this->assertEquals(
            Response::HTTP_OK,
            $result['status'],
            (!empty($result['message']))
                ? $result['message']
                : 'Неизвестная ошибка'
        );
    }
}
