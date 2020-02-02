<?php

namespace App\Tests\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\Rollback;

class AdminControllerCategoriesTest extends WebTestCase
{

    use Rollback;

    public function testTextOnPage()
    {
        $crawler = $this->client->request('GET', '/admin/su/categories');

        $this->assertSame("Categories list", $crawler->filter("h2")->text());
        $this->assertContains("Electronics", $this->client->getResponse()->getContent());
    }
}
