<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:users:list',
    description: 'Add a short description for your command',
)]
class UsersListCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $table = $io->createTable();
        $table->setHeaders(['Id', 'Email', 'Roles']);

        foreach ($this->userRepository->findAll() as $user) {
            $table->addRow([$user->getId(), $user->getEmail(), join(', ', $user->getRoles())]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}
