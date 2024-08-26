<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-user', description: 'Create user for test',)]
class UserCreateCommand extends Command {

    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly EntityManagerInterface $manager
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = new User();
        $user->setUsername("Admin")
             ->setEmail("admin@gmail.com")
             ->setRoles(["ROLE_ADMIN"]);

        $hashPassword = $this->hasher->hashPassword($user, "admin");
        $user->setPassword($hashPassword);

        $this->manager->persist($user);
        $this->manager->flush();

        $io->success("L'utilisateur à bien été créer !");
        return Command::SUCCESS;
    }
}
