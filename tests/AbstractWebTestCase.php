<?php

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AbstractWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected EntityManager $entityManager;

    protected UserRepository $userRepository;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient([], [
            'HTTPS' => 'on',
        ]);
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    protected function loginAsAdmin(): void
    {
        $this->client->loginUser($this->userRepository->findOneBy([
            'username' => 'Warez',
        ]));
    }

    protected function loginAsUser(): void
    {
        $this->client->loginUser($this->userRepository->findOneBy([
            'username' => 'John Doe',
        ]));
    }

    /**
     *
     * @param string $route
     * @param string $button
     * @param array $data
     * @param bool $loggedIn
     * @return Response
     */
    protected function submitForm(string $route, string $button, array $data, bool $loggedIn = true): Response
    {
        $crawler = $this->client->request('GET', $route);

        if (! $loggedIn) {
            $this->client->getCookieJar()
                ->clear();
        }

        $form = $crawler->selectButton($button)
            ->form();
        $this->client->submit($form, $data);

        return $this->client->getResponse();
    }
}
