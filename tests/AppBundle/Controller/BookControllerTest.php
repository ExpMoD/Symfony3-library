<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    const USERNAME = 'username';
    const PASSWORD = 'password';

    public function testBookAdd()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $loginForm = $crawler
            ->selectButton('_submit')
            ->form([
                '_username' => self::USERNAME,
                '_password' => self::PASSWORD,
            ]);

        $client->submit($loginForm);
        $this->assertTrue($client->getResponse()->isRedirect());

        $crawler = $client->followRedirect();

        $link = $crawler->filter('a:contains("Добавить книгу")');

        $this->assertGreaterThan(0, $link->count(), 'Ссылка "Добавление книги" не найдена');

        $crawler = $client->click($link->link());

        $this->assertGreaterThan(0, $crawler->filter('form[name="app_bundle_book"]')->count(), 'Форма не найдена');

        $bookAddForm = $crawler
            ->selectButton('Добавить')
            ->form([
                'app_bundle_book[name]' => 'Название книги ТЕСТ',
                'app_bundle_book[author]' => 'Автор книги ТЕСТ',
                'app_bundle_book[dateOfReading][date][year]' => '2014',
                'app_bundle_book[dateOfReading][date][month]' => '2',
                'app_bundle_book[dateOfReading][date][day]' => '21',
            ]);

        $client->submit($bookAddForm);
        $this->assertTrue($client->getResponse()->isRedirect(), 'Форма заполнена неверно');
    }
}
