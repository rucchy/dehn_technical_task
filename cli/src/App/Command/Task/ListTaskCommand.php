<?php

namespace Console\App\Command\Task;

use Console\App\Command\BaseCommand;
use Console\App\Entity\Task;
use Console\App\Repository\TaskRepository;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A console command that shows tasks stored.
 *
 * To use this command, open a terminal window, enter into your project
 * directory and execute the following:
 *
 *     $ php bin/console list-tasks
 *
 */
#[AsCommand(
    name: 'app:list-tasks',
    description: 'Show all the task saved'
)]
class ListTaskCommand extends BaseCommand
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
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
        $this->setHelp($this->getCommandHelp());
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
        $tasks = $this->taskRepository->findAll();
        $tableRows = [];
        foreach ($tasks as $task) {
            /** @var Task $task */
            $tableRows[] = [
                $task->getId(),
                $task->getTitle(),
                $task->getDescription(),
                $task->getDueDate()->format("Y-m-d"),
                $task->getStatus()->value
            ];
        }
        $table = new Table($output);
        $table
            ->setHeaders(['ID', 'Title', 'Description', 'Due Date', 'Status'])
            ->setRows($tableRows);
        $table->render();
        return Command::SUCCESS;
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
            The <info>%command.name%</info> command shows all tasks stored:

            <info>php %command.full_name%</info>

            HELP;
    }
}