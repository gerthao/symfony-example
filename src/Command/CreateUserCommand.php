<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Add a short description for your command',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly UserRepository              $users)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email')
            ->addArgument('password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $io       = new SymfonyStyle($input, $output);
        $email    = $input->getArgument('email');
        $password = $input->getArgument('password');

        if ($email) $io->note(sprintf('You passed an argument: %s', $email));
        if ($password) $io->note(sprintf('You passed an argument: %s', $password));

        User::withUser(fn(User $u) => $this->users->save(
            $u
                ->setEmail($email)
                ->setPassword($this->userPasswordHasher->hashPassword($u, $password))
        ));

        $io->success('User account was created!');

        return Command::SUCCESS;
    }
}
