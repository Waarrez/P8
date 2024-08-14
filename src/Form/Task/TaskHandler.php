<?php

declare(strict_types=1);

namespace App\Form\Task;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TaskHandler
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly Security $security,
    ) {
    }

    public function prepare(Task $data, array $options = []): FormInterface
    {
        return $this->formFactory->create(TaskType::class, $data, $options);
    }

    public function handleCreate(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);

        $user = $this->security->getUser();

        if (! $user instanceof User) {
            throw new AccessDeniedException('Vous devez être connecté pour créer une tâche.');
        }

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Task $task */
            $task = $form->getData();
            $task->setUser($user);
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    public function handleUpdate(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);

        $user = $this->security->getUser();

        if (! $user instanceof User) {
            throw new AccessDeniedException('Vous devez être connecté pour modifier une tâche.');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Task $task */
            $task = $form->getData();
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }
}
