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
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TaskController extends AbstractController
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly EntityManagerInterface $em,
        private readonly TaskHandler $taskHandler,
    )
    {}

    #[Route("/tasks", name : "task_list", methods: ['GET'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function listAction(): Response
    {


        return $this->render('task/list.html.twig', ['tasks' => $this->taskRepository->getTasksNotFinished()]);
    }

    #[Route(path: "/tasks/create", name: "task_create", methods: ['GET', 'POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
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

    #[Route(path: "/task/{id}/edit", name: 'task_edit', methods: ['GET', 'POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function editAction(Task $task, Request $request): Response
    {
        if($this->getUser()->getId() !== $task->getUser()->getId()) {
            $this->addFlash('error', "Vous n'êtes pas auteur de la tâche, vous ne pouvez pas la modifier");
            return $this->redirectToRoute("task_list");
        }


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

    #[Route(path: "/tasks/{id}/toggle", name: 'task_toggle', methods: ['GET', 'POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function toggleTaskAction(Task $task): Response
    {
        $task->setDone(!$task->isDone());
        $this->em->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    #[Route(path: "/tasks/finished", name: "task_finished", methods: ['GET'])]
    public function viewTaskFinished(): Response {

        $tasks = $this->taskRepository->getTasksFinished();

        return $this->render('task/finished.html.twig', [
            'tasks' => $tasks
        ]);
    }

    #[Route(path: "/tasks/{id}/delete", name: 'task_delete', methods: ['GET', 'POST'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    public function deleteTaskAction(Task $task): RedirectResponse
    {
        if($this->getUser()->getId() !== $task->getUser()->getId()) {
            $this->addFlash('error', "Vous n'êtes pas autorisé à faire cette action");

            return $this->redirectToRoute('task_list');
        }

        $this->em->remove($task);
        $this->em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}