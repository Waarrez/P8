<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTestCase extends AbstractWebTestCase
{
    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);
        $this->userRepository = $userRepository;
    }

    public function getEditableUser(): User
    {
        return $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->where('u.username != :username')
            ->setParameter('username', 'Warez')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()[0];
    }

    public function submitForm(string $route, string $button, array $data, bool $loggedIn = true): Response
    {
        $crawler = $this->client->request('GET', $route);

        if (! $loggedIn) {
            $this->client->getCookieJar()
                ->clear();
        }

        $form = $crawler->selectButton($button)
            ->form()
            ->disableValidation();
        $this->client->submit($form, $data);

        return $this->client->getResponse();
    }
}
