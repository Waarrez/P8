<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Tests\AbstractWebTestCase;

class TaskTestCase extends AbstractWebTestCase
{
    protected TaskRepository $taskRepository;

    protected ?Task $adminTask = null;

    protected ?Task $userTask = null;

    protected function setUp(): void
    {
        parent::setUp();
        /** @var TaskRepository $taskRepository */
        $taskRepository = $this->entityManager->getRepository(Task::class);
        $this->taskRepository = $taskRepository;
        $this->adminTask = $this->taskRepository->findOneBy([
            'user' => $this->userRepository->findOneBy([
                'username' => 'Admin',
            ]),
        ]);
        $this->userTask = $this->taskRepository->findOneBy([
            'user' => $this->userRepository->findOneBy([
                'username' => 'John Doe',
            ]),
        ]);
    }
}
