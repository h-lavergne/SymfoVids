<?php

namespace App\Tests\Controllers;

use App\Tests\RoleAdmin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerCategoriesTest extends WebTestCase
{

    use RoleAdmin;

    public function testTextOnPage()
    {
        $crawler = $this->client->request('GET', '/admin/su/categories');

        $this->assertSame("Categories list", $crawler->filter("h2")->text());
        $this->assertContains("Electronics", $this->client->getResponse()->getContent());
    }
}
