<?php

namespace App\Tests\Controllers\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerSecurityTest extends WebTestCase
{
    /**
     * @param string $httpMethod
     * @param string $url
     * @dataProvider getUrlsForRegularUsers
     */
    public function testAccessDeniedForRegularUsers(string $httpMethod, string $url)
    {
        $client = static::createClient([], [
            "PHP_AUTH_USER" => "leo@gmail.com",
            "PHP_AUTH_PW" => "password",
        ]);
        $client->disableReboot();
        $client->request($httpMethod, $url);

        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function getUrlsForRegularUsers()
    {
        yield["GET", "/admin/su/categories"];
        yield["GET", "/admin/su/edit-category/1"];
        yield["GET", "/admin/su/delete-category/1"];
        yield["GET", "/admin/su/users"];
    }

    public function testAdminSu()
    {
        $client = static::createClient([], [
            "PHP_AUTH_USER" => "hugo@gmail.com",
            "PHP_AUTH_PW" => "password"
        ]);
        $crawler = $client->request("GET", "/admin/su/categories");
        $this->assertSame("Categories list", $crawler->filter("h2")->text());

    }
}
