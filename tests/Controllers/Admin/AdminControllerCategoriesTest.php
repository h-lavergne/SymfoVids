<?php

namespace App\Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerCategoriesTest extends WebTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->client = static::createClient([], [
            "PHP_AUTH_USER" => 'hugo@gmail.com',
            "PHP_AUTH_PW" => 'password',
        ]);
        $this->client->disableReboot();

    }

    public function testTextOnPage()
    {
        $crawler = $this->client->request('GET', '/admin/su/categories');

        $this->assertSame("Categories list", $crawler->filter("h2")->text());
        $this->assertContains("Electronics", $this->client->getResponse()->getContent());
    }
}
