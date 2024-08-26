<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Task;

class EditTaskTest extends TaskControllerTestCase
{

    public function testShowEditTaskPage(): void
    {
        $this->loginAsUser();

        $this->client->request('GET', sprintf('/task/%s/edit', $this->userTask->getId()));
        $response = $this->client->getResponse();

        static::assertEquals(200, $response->getStatusCode());
        static::assertStringContainsString('Modifier', $response->getContent());
        static::assertStringContainsString($this->userTask->title, $response->getContent());
        static::assertStringContainsString($this->userTask->content, $response->getContent());
    }

    public function testShowEditTaskLoggedIn(): void
    {
        $this->client->request('GET', sprintf('/task/%s/edit', $this->userTask->getId()));
        $response = $this->client->getResponse();

        static::assertEquals(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('https://localhost/login'));
    }

    public function testShowEditTaskWithBadId(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/task/bad-id/edit');
        $response = $this->client->getResponse();

        static::assertEquals(404, $response->getStatusCode());
    }

    public function testEditTask(): void
    {
        $this->loginAsUser();
        $randomString = uniqid(__FUNCTION__, true);
        $id = $this->userTask->getId();

        $response = $this->submitForm(sprintf('/task/%s/edit', $id), 'Modifier', [
            'task' => [
                'title' => $randomString,
                'content' => $randomString,
            ],
        ]);

        $updatedTask = $this->taskRepository->findOneBy([
            'id' => $id,
        ]);

        static::assertEquals(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('/tasks'));
        static::assertEquals($randomString, $updatedTask->title);
        static::assertEquals($randomString, $updatedTask->content);
    }

    /**
     * @dataProvider editTaskNotValid_dataProvider
     */
    public function testEditTaskNotValid(string $title, string $content): void
    {
        $this->loginAsUser();
        $id = $this->userTask->getId();

        $response = $this->submitForm(sprintf('/task/%s/edit', $id), 'Modifier', [
            'task' => [
                'title' => $title,
                'content' => $content,
            ],
        ]);


        /** @var Task $updatedTask */
        $updatedTask = $this->taskRepository->findOneBy([
            'id' => $id,
        ]);

        static::assertEquals(500, $response->getStatusCode());
        static::assertNotEquals($title, $updatedTask->title);
        static::assertNotEquals($content, $updatedTask->content);
    }

    public function editTaskNotValid_dataProvider(): array
    {
        return [['test_title', ''], ['', 'test_content'], ['', '']];
    }

    public function testAdminUpdateTaskWithoutAuthor(): void
    {
        $this->loginAsAdmin();

        $randomString = uniqid(__FUNCTION__, true);
        $id = $this->adminTask->getId();

        $response = $this->submitForm(sprintf('/task/%s/edit', $id), 'Modifier', [
            'task' => [
                'title' => $randomString,
                'content' => $randomString,
            ],
        ]);

        $updatedTask = $this->taskRepository->findOneBy([
            'id' => $id,
        ]);

        static::assertEquals(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('/tasks'));
        static::assertEquals($randomString, $updatedTask->title);
        static::assertEquals($randomString, $updatedTask->content);
    }

    public function testUserCantEditTaskPageOfAnotherUser(): void
    {
        $this->loginAsUser();

        $this->client->request('GET', sprintf('/task/%s/edit', $this->adminTask->getId()));
        $response = $this->client->getResponse();

        static::assertEquals(302, $response->getStatusCode());
        static::assertTrue($response->isRedirect('/tasks'));
    }
}
