<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Task;

class CreateTaskTest extends TaskTestCase
{
    public function testShowCreateTaskPage(): void
    {
        $this->loginAsUser();

        $this->client->request('GET', '/tasks/create');
        $response = $this->client->getResponse();

        static::assertEquals(200, $response->getStatusCode());
        static::assertStringContainsString('Ajouter', $response->getContent());
        static::assertStringContainsString('Retour à la liste des tâches', $response->getContent());
    }

    public function testShowCreateTaskPageWithoutBeLoggedIn(): void
    {
        $this->client->request('GET', '/tasks/create');

        $response = $this->client->getResponse();

        static::assertEquals(302, $response->getStatusCode());

        static::assertStringContainsString('/login', $response->headers->get('Location'));
    }

    public function testCreateTask(): void
    {
        $this->loginAsUser();

        $randomString = uniqid(__FUNCTION__, true);

        $crawler = $this->client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form();

        $form['task[title]'] = $randomString;
        $form['task[content]'] = $randomString;

        $this->client->submit($form);

        $response = $this->client->getResponse();

        $task = $this->taskRepository->findOneBy([
            'title' => $randomString,
            'content' => $randomString,
        ]);

        static::assertInstanceOf(Task::class, $task);
        static::assertSame('John Doe', $task->getUser()->getUsername());
        static::assertEquals(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('/tasks'));
    }

    public function testCreateTaskLogged(): void
    {
        $this->loginAsUser();
        $randomString = uniqid(__FUNCTION__, true);

        $response = $this->submitForm('/tasks/create', 'Ajouter', [
            'task' => [
                'title' => $randomString,
                'content' => $randomString,
            ],
        ], false);
        $task = $this->taskRepository->findOneBy([
            'title' => $randomString,
            'content' => $randomString,
        ]);

        static::assertNull($task);
        static::assertEquals(302, $response->getStatusCode());
        static::assertStringContainsString('/login', $response->headers->get('Location'));
    }

    /**
     * @dataProvider createTaskNotValid_dataProvider
     */
    public function testCreateTaskNotValid(string $title, string $content): void
    {
        $this->loginAsUser();

        $response = $this->submitForm('/tasks/create', 'Ajouter', [
            'task' => [
                'title' => $title,
                'content' => $content,
            ],
        ]);
        $task = $this->taskRepository->findOneBy([
            'title' => $title,
            'content' => $content,
        ]);

        static::assertNull($task);
        static::assertEquals(200, $response->getStatusCode());
        static::assertStringContainsString('Ajouter', $response->getContent());
        static::assertStringContainsString('Retour à la liste des tâches', $response->getContent());
    }

    public function createTaskNotValid_dataProvider(): array
    {
        return [['Mon titre', ''], ['', 'Mon contenu'], ['', '']];
    }
}
