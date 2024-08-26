<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\User\UserHandler;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private readonly UserHandler $userHandler,
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $manager
    ) {
    }

    #[Route(path: '/users', name: 'user_list', methods: ['GET'])]
    public function list(): Response
    {
        return $this->render('user/list.html.twig', [
            'users' => $this->userRepository->findAll(),
        ]);
    }

    #[Route(path: '/users/create', name: 'user_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $user = new User();
        $form = $this->userHandler->prepare($user);
        $isCreated = $this->userHandler->handle($form, $request);

        if ($isCreated) {
            $this->addFlash('success', "L'utilisateur a bien été ajouté.");
            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/users/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request): Response
    {
        $form = $this->userHandler->prepare($user);
        $updatedForm = $this->userHandler->handle($form, $request);

        if ($updatedForm) {
            $this->addFlash('success', "L'utilisateur a bien été modifié");
            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route(path: '/users/{id}/delete', name: 'user_delete', methods: ['GET'])]
    public function delete(User $user): Response
    {
        $user = $this->userRepository->find($user);

        $this->manager->remove($user);
        $this->manager->flush();

        $this->addFlash('success', 'L\'utilisateur à bien été supprimé');

        return $this->redirectToRoute('user_list');
    }
}
