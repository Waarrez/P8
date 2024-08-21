<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;

class EditUserTest extends UserControllerTestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->getEditableUser();
    }


    public function testShowEditUserPage(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', sprintf('/users/%s/edit', $this->user->id));
        $response = $this->client->getResponse();

        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString('Modifier', $response->getContent());
        static::assertStringContainsString($this->user->username, $response->getContent());
    }


    public function testShowEditUserPageWithWrongId(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/users/wrong-id/edit');
        $response = $this->client->getResponse();

        static::assertSame(404, $response->getStatusCode());
    }

    public function testEditUser(): void
    {

        $this->loginAsAdmin();
        $randomString = uniqid('', true);

        $response = $this->submitForm(sprintf('/users/%s/edit', $this->user->id), 'Modifier', [
            'user_form' => [
                'username' => $randomString,
                'password' => [
                    'first' => $randomString,
                    'second' => $randomString,
                ],
                'email' => sprintf('%s@gmail.com', $randomString),
                'roles' => ['ROLE_USER'],
            ],
        ]);

        $updatedUser = $this->userRepository->findOneBy([
            'username' => $randomString,
        ]);

        static::assertSame(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('/users'));
        static::assertSame($randomString, $updatedUser->username);
        static::assertSame(sprintf('%s@gmail.com', $randomString), $updatedUser->email);
    }

    public function testEditUserTooLongUsername(): void
    {
        $this->loginAsAdmin();

        $response = $this->submitForm(sprintf('/users/%s/edit', $this->user->id), 'Modifier', [
            'user_form' => [
                'username' => 'un-très-très-très-long-username',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'email@test.fr',
                'roles' => ['ROLE_USER'],
            ],
        ]);
        $user = $this->userRepository->findOneBy([
            'username' => 'un-très-très-très-long-username',
            'email' => 'email@gmail.com',
        ]);

        static::assertNull($user);
        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString(
            'Le nom d&#039;utilisateur ne doit pas dépasser 25 caractères',
            $response->getContent()
        );
    }

    public function testEditUserDifferentPassword(): void
    {
        // Given
        $this->loginAsAdmin();

        // When
        $response = $this->submitForm(sprintf('/users/%s/edit', $this->user->id), 'Modifier', [
            'user_form' => [
                'username' => 'username',
                'password' => [
                    'first' => 'password-1',
                    'second' => 'password-2',
                ],
                'email' => 'email@test.fr',
                'roles' => ['ROLE_USER'],
            ],
        ]);
        $user = $this->userRepository->findOneBy([
            'username' => 'un-très-très-très-long-username',
            'email' => 'email@test.fr',
        ]);

        // Then
        static::assertNull($user);
        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString('Les mots de passe doivent être identique !', $response->getContent());
    }


    public function testEditUserWithBadFormatForEmail(): void
    {
        $this->loginAsAdmin();

        $response = $this->submitForm('/users/create', 'Ajouter', [
            'user_form' => [
                'username' => 'username',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'wrong-email',
                'roles' => ['ROLE_USER'],
            ],
        ]);
        $user = $this->userRepository->findOneBy([
            'username' => 'username',
            'email' => 'wrong-email',
        ]);

        static::assertNull($user);
        static::assertSame(200, $response->getStatusCode());
        static::assertStringContainsString(
            'Le format de l&#039;adresse n&#039;est pas correcte.',
            $response->getContent()
        );
    }

    public function testEditUserLongEmail(): void
    {
        $this->loginAsAdmin();

        $response = $this->submitForm(sprintf('/users/%s/edit', $this->user->id), 'Modifier', [
            'user_form' => [
                'username' => 'username',
                'password' => [
                    'first' => 'password',
                    'second' => 'password',
                ],
                'email' => 'test-dun-tres-tres-long-long-long-long-long-long-long-long-long-long-format-pour-lemail@gmail.com',
                'roles' => ['ROLE_USER'],
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
}
