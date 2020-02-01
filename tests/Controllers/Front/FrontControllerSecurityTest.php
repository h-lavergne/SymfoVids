<?php

namespace App\Tests\Controllers\Front;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerSecurityTest extends WebTestCase
{
    /**
     * @param string $url
     * @dataProvider getSecureUrls
     */
    public function testSecureUrls(string $url)
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $this->assertContains("/login", $client->getResponse()->getTargetUrl());
    }

    public function getSecureUrls()
    {
        yield ["/admin/videos"];
        yield ["/admin"];
        yield ["/admin/su/categories"];
        yield ["/admin/su/users"];
        yield ["/admin/su/delete-category/2"];
    }

    public function testVideoForMembersOnly()
    {
        $client = static::createClient();
        $client->request("GET", '/video-list/category-4/movies');
        $this->assertContains("in order to like and comment", $client->getResponse()->getContent());

    }
}
