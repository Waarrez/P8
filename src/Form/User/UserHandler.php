<?php

namespace App\Form\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserHandler
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly EntityManagerInterface $manager
    ) {
    }

    public function prepare(User $data, array $options = []): FormInterface
    {
        return $this->formFactory->create(UserFormType::class, $data, $options);
    }

    public function handle(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            if ($user->password === null) {
                $form->addError(new FormError('Vous devez saisir un mot de passe.'));
                return false;
            }

            $user->password = $this->hasher->hashPassword($user, $user->password);
            $this->manager->persist($user);
            $this->manager->flush();

            return true;
        }

        return false;
    }
}