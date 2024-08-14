<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\Task\TaskHandler;
use App\Form\Task\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly EntityManagerInterface $em,
        private readonly TaskHandler $taskHandler,
    )
    {}

    #[Route("/tasks", name : "task_list")]
    public function listAction(): Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $this->taskRepository->findAll()]);
    }

    #[Route(path: "/tasks/create", name: "task_create")]
    public function createAction(Request $request): Response
    {
        $task = new Task();
        $form = $this->taskHandler->prepare($task);
        $isCreated = $this->taskHandler->handleCreate($form, $request);

        if ($isCreated) {
            $this->addFlash('success', 'La tâche a été bien été ajoutée.');
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: "/task/{id}/edit", name: 'task_edit')]
    public function editAction(Task $task, Request $request): Response
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route(path: "/tasks/{id}/toggle", name: 'task_toggle')]
    public function toggleTaskAction(Task $task): Response
    {
        $task->setDone(!$task->isDone());
        $this->em->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    #[Route(path: "/tasks/{id}/delete", name: 'task_delete')]
    public function deleteTaskAction(Task $task): RedirectResponse
    {
        $this->em->remove($task);
        $this->em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}