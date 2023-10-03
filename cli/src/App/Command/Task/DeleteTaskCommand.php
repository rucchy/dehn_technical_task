<?php

namespace Console\App\Command\Task;

use Console\App\Command\BaseCommand;
use Console\App\Repository\TaskRepository;
use Console\App\Utils\EntityManagerInterface;
use Console\App\Validator\TaskValidator;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Uid\UuidV4;

/**
 * A console command that deletes tasks and stores them.
 *
 * To use this command, open a terminal window, enter into your project
 * directory and execute the following:
 *
 *     $ php bin/console delete-task
 *
 */
#[AsCommand(
    name: 'app:delete-task',
    description: 'Delete tasks'
)]
class DeleteTaskCommand extends BaseCommand
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TaskValidator          $validator,
        private readonly TaskRepository         $taskRepository,
    )
    {
        parent::__construct();
    }

    /**
     * This method configure the current method.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setHelp($this->getCommandHelp())
            ->addArgument('id', InputArgument::OPTIONAL, 'The id of the task');
    }

    /**
     * This method is executed after initialize() and before execute(). Its purpose
     * is to check if some of the options/arguments are missing and interactively
     * ask the user for those values.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {

        $this->io->title('Delete Task Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console delete-task id',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
        ]);

        // Ask for the id if it's not defined
        $id = $input->getArgument('id');
        if (null !== $id) {
            $this->io->text(' > <info>ID</info>: ' . $id);
            $this->validator->validateID($id);
        } else {
            $id = $this->io->ask('ID', null, $this->validator->validateID(...));
            $input->setArgument('id', $id);
        }
    }

    /**
     * This method is executed after interact() and initialize().
     * Contains the logic to execute to complete this command task.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var String $id */
        $id = $input->getArgument('id');

        // recover the task
        $task = $this->taskRepository->findOne(UuidV4::fromString($id));
        if ($task) {
            if ($this->entityManager->delete($task)) {
                $this->io->success(sprintf('The task with the id %s was successfully deleted', $task->getId()));
                return Command::SUCCESS;
            }
        } else {
            $this->io->error(sprintf("There isn't any task with the ID: %s.", $id));
            return Command::FAILURE;
        }
        $this->io->error("Something goes wrong and the task doesn't delete.");
        return Command::FAILURE;
    }

    /**
     * The command help is usually included in the configure() method, but when
     * it's too long, it's better to define a separate method to maintain the
     * code readability.
     *
     * @return string
     */
    private function getCommandHelp(): string
    {
        return <<<'HELP'
            The <info>%command.name%</info> command deletes a tasks:

            <info>php %command.full_name%</info> <comment>id</comment>

            If you omit the required argument, the command will ask you to
            provide the missing value:

              # command will ask you for the arguments
              <info>php %command.full_name%</info>

            HELP;
    }
}