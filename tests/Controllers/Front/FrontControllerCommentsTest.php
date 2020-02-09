<?php

namespace App\Tests\Controllers;

use App\Tests\RoleUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerCommentsTest extends WebTestCase
{
    use RoleUser;

    public function testNotLoggedInUser()
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request("GET", "/video-details/10");

        $form = $crawler->selectButton("Add")->form([
            "comment" => "test comment"
        ]);
        $client->submit($form);

        $this->assertContains("Please sign in", $client->getResponse()->getContent());
    }

    public function testNewCommentAndNumberOfComments()
    {
        $this->client->followRedirects();
//        $crawler = $this->client->request("GET", "/video-details/16");
//
//        $form = $crawler->selectButton("Add")->form([
//            "comment" => "Test comment"
//        ]);
//
//        $this->client->submit($form);
//        dd($this->client->getResponse()->getContent());
//        $this->assertContains("Test comment", $this->client->getResponse()->getContent());

        $crawler = $this->client->request("GET", "/video-list/category-2/toys");

        $this->assertSame("Comments (0)", $crawler->filter("a.test")->text());
    }
}
