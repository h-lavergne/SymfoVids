<?php

namespace App\Tests;

trait RoleUser
{
    public function setUp()
    {
        parent::setUp();
        $this->client = static::createClient([], [
            "PHP_AUTH_USER" => 'leo@gmail.com',
            "PHP_AUTH_PW" => 'password',
        ]);
        $this->client->disableReboot();

        $this->entityManager = $this->client->getContainer()->get("doctrine.orm.entity_manager");

    }

    public function tearDown()
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}