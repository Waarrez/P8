<?php

declare(strict_types=1);

namespace App\Tests\Controller;

class ListUsersTest extends UserControllerTestCase
{

    public function testListOfUsers(): void
    {

        $this->loginAsAdmin();

        $this->client->request('GET', '/users');
        $response = $this->client->getResponse();

        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString('Liste des utilisateurs', $response->getContent());
    }

    public function testNoAdminListUsers(): void
    {

        $this->loginAsUser();

        $this->client->request('GET', '/users');
        $response = $this->client->getResponse();

        static::assertEquals(403, $response->getStatusCode());
    }
}
