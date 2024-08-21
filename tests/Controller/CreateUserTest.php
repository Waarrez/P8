<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;

class CreateUserTest extends UserControllerTestCase
{
    public function testAdminCreateUserPage(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/users/create');
        $response = $this->client->getResponse();

        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString('Créer un utilisateur', $response->getContent());
    }

    public function testUserCantCreateUserPage(): void
    {
        $this->loginAsUser();

        $this->client->request('GET', '/users/create');
        $response = $this->client->getResponse();

        static::assertSame(403, $response->getStatusCode());
    }

    public function testCreateUser(): void
    {

        $this->loginAsAdmin();
        $randomString = uniqid('', true);

        $this->submitForm('/users/create', 'Ajouter', [
            'user_form' => [
                'username' => $randomString,
                'password' => [
                    'first' => $randomString,
                    'second' => $randomString,
                ],
                'email' => sprintf('%s@test.fr', $randomString),
                'roles' => ['ROLE_USER'],
            ],
        ]);
        $response = $this->client->getResponse();
        $newUser = $this->userRepository->findOneBy([
            'username' => $randomString,
        ]);

        static::assertInstanceOf(User::class, $newUser);
        static::assertSame(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('/users'));
    }

    public function testCreateUserExistedUsername(): void
    {

        $this->loginAsAdmin();

        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user_form' => [
                'username' => 'Warez',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'warez@gmail.fr',
            ],
        ]);

        $user = $this->userRepository->findOneBy([
            'username' => 'Warez',
            'email' => 'warez@gmail.fr',
        ]);

        static::assertNull($user);
        static::assertSame(200, $response->getStatusCode());
    }

    public function testCreateUserTooLongUsername(): void
    {

        $this->loginAsAdmin();

        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user_form' => [
                'username' => 'test-dun-très-très-très-long-username',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'email@gmail.com',
            ],
        ]);

        $user = $this->userRepository->findOneBy([
            'username' => 'test-dun-très-très-très-long-username',
            'email' => 'email@gmail.com',
        ]);

        static::assertNull($user);
        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString(
            'Le nom d&#039;utilisateur ne doit pas dépasser 25 caractères',
            $response->getContent()
        );
    }

    public function testCreateUserWithDifferentPasswords(): void
    {

        $this->loginAsAdmin();

        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user_form' => [
                'username' => 'username',
                'password' => [
                    'first' => 'password1',
                    'second' => 'password2',
                ],
                'email' => 'email@gmail.com',
            ],
        ]);
        $user = $this->userRepository->findOneBy([
            'username' => 'username',
            'email' => 'email@gmail.com',
        ]);

        static::assertNull($user);
        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString('Les mots de passe doivent être identique !', $response->getContent());
    }

    public function testCreateUserBadFormatEmail(): void
    {

        $this->loginAsAdmin();

        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user_form' => [
                'username' => 'username',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'badformat',
            ],
        ]);
        $user = $this->userRepository->findOneBy([
            'username' => 'username',
            'email' => 'badformat',
        ]);

        static::assertNull($user);
        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString(
            'Le format de l&#039;adresse n&#039;est pas correcte.',
            $response->getContent()
        );
    }

    public function testCreateUserWithTooLongEmail(): void
    {
        $this->loginAsAdmin();

        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user_form' => [
                'username' => 'username',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'test-dun-tres-tres-long-long-long-long-long-long-long-long-long-long-format-pour-lemail@gmail.com',
            ],
        ]);

        $user = $this->userRepository->findOneBy([
            'username' => 'username',
            'email' => 'test-dun-tres-tres-long-long-long-long-long-long-long-long-long-long-format-pour-lemail@gmail.com',
        ]);

        static::assertNull($user);
        static::assertSame(200, $response->getStatusCode());

        static::assertStringContainsString(
            'This value is too long. It should have 60 characters or less.',
            $response->getContent()
        );
    }

    public function testCreateUserNullPassword(): void
    {

        $this->loginAsAdmin();

        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user_form' => [
                'username' => 'username',
                'password' => [
                    'first' => null,
                    'second' => null,
                ],
                'email' => 'email@gmail.com',
            ],
        ]);
        $user = $this->userRepository->findOneBy([
            'username' => 'username',
            'email' => 'email@gmail.com',
        ]);

        static::assertNull($user);
        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString('Vous devez saisir un mot de passe', $response->getContent());
    }

    public function testCreateUserWithRoleAdmin(): void
    {
        $this->loginAsAdmin();
        $randomString = uniqid('', true);

        $this->submitForm('/users/create', 'Ajouter', [
            'user_form' => [
                'username' => $randomString,
                'password' => [
                    'first' => $randomString,
                    'second' => $randomString,
                ],
                'email' => sprintf('%s@gmail.com', $randomString),
                'roles' => ['ROLE_ADMIN'],
            ],
        ]);

        $response = $this->client->getResponse();
        $newUser = $this->userRepository->findOneBy([
            'username' => $randomString,
        ]);

        static::assertInstanceOf(User::class, $newUser);

        static::assertSame(['ROLE_USER', 'ROLE_ADMIN'], $newUser->getRoles());
        static::assertSame(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('/users'));
    }
}
