<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\User\UserHandler;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private readonly UserHandler $userHandler,
        private readonly UserRepository $userRepository,
    ) {
    }

    #[Route(path: '/users', name: 'user_list')]
    public function list(): Response
    {
        return $this->render('user/list.html.twig', [
            'users' => $this->userRepository->findAll(),
        ]);
    }

    #[Route(path: '/users/create', name: 'user_create')]
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

    #[Route(path: '/users/{id}/edit', name: 'user_edit')]
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
}
