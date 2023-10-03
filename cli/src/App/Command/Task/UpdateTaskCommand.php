<?php

namespace Console\App\Command\Task;

use Console\App\Command\BaseCommand;
use Console\App\Entity\Task;
use Console\App\Entity\TaskState;
use Console\App\Repository\TaskRepository;
use Console\App\Utils\EntityManagerInterface;
use Console\App\Validator\TaskValidator;
use DateTime;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
    name: 'app:update-task',
    description: 'Update tasks'
)]
class UpdateTaskCommand extends BaseCommand
{
    /**
     * @var Task
     */
    private Task $task;

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
            ->addArgument('id', InputArgument::OPTIONAL, 'The id of the task')
            ->addOption('title', null, InputOption::VALUE_REQUIRED, "The new title for the task")
            ->addOption('description', null, InputOption::VALUE_REQUIRED, "The new description for the task")
            ->addOption('dueDate', null, InputOption::VALUE_REQUIRED, "The new due date for the task. The format has to be Y-m-d.")
            ->addOption('completed', null, InputOption::VALUE_NONE, "Mark the task as completed");
    }

    /**
     * This method is executed after initialize() and before execute(). Its purpose
     * is to check if some of the options/arguments are missing and interactively
     * ask the user for those values.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws Exception
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {

        $this->io->title('Update Task Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console update-task id --title="New title" --description="New description" --dueDate="2023-10-09" --completed',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
        ]);

        // Ask for the id if it's not defined
        $id = $input->getArgument('id');
        if (null !== $id) {
            $this->io->text(' > <info>ID</info>: ' . $id);
            $this->validateID($id);
        } else {
            $id = $this->io->ask('ID', null, $this->validateID(...));
            $input->setArgument('id', $id);
        }

        $title = $input->getOption('title');
        $description = $input->getOption('description');
        $dueDate = $input->getOption('dueDate');
        $completed = $input->getOption('completed');
        //If at least one option is set, only update that option
        if (null !== $title || null !== $description || null != $dueDate || $completed) {
            if (null !== $title) {
                $this->io->text(' > <info>New title</info>: ' . $title);
                $this->validator->validateTitle($title);
            }
            if (null !== $description) {
                $this->io->text(' > <info>New description</info>: ' . $description);
                $this->validator->validateDescription($description);
            }
            if (null !== $dueDate) {
                $this->io->text(' > <info>New due date</info>: ' . $dueDate);
                $this->validator->validateDueDate($dueDate);
            }
            if ($completed) {
                $this->io->text(' > <info>Mark as completed</info>: yes');
            }
            //If there aren't any option set, ask for all fields
        } else {
            $title = $this->io->ask('Title', $this->task->getTitle(), $this->validator->validateTitle(...));
            $input->setOption('title', $title);

            $description = $this->io->ask('Description', $this->task->getDescription(), $this->validator->validateDescription(...));
            $input->setOption('description', $description);

            $dueDate = $this->io->ask('Due date (Y-m-d)', $this->task->getDueDate()->format("Y-m-d"), $this->validator->validateDueDate(...));
            $input->setOption('dueDate', $dueDate);

            if ($this->task->getStatus() == TaskState::PENDING) {
                $completed = $this->io->confirm('Mark as completed', false);
                $input->setOption('completed', $completed);
            } else {
                $this->io->text(' > <info>Already completed</info>');
            }
        }
    }

    /**
     *  This method is executed after interact() and initialize().
     *  Contains the logic to execute to complete this command task.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $title */
        $title = $input->getOption('title');

        /** @var string $description */
        $description = $input->getOption('description');

        /** @var string $dueDate */
        $dueDate = $input->getOption('dueDate');

        /** @var bool $completed */
        $completed = $input->getOption('completed');

        // update the task
        if ($title) {
            $this->task->setTitle($title);
        }
        if ($description) {
            $this->task->setDescription($description);
        }
        if ($dueDate) {
            $this->task->setDueDate(new DateTime($dueDate));
        }
        if ($completed) {
            $this->task->setStatus(TaskState::COMPLETED);
        }
        if ($this->entityManager->persist($this->task)) {
            $this->io->success(sprintf('The task with the id %s was successfully updated', $this->task->getId()));
            return Command::SUCCESS;
        }

        $this->io->error("Something goes wrong and the task doesn't update.");
        return Command::FAILURE;
    }

    /**
     * Method that validate the ID and set it for the class
     * @param string $id
     * @return string
     * @throws Exception
     */
    private function validateId(string $id): string
    {
        $this->validator->validateID($id);
        $task = $this->taskRepository->findOne(UuidV4::fromString($id));

        if ($task) {
            $this->task = $task;
        } else {
            throw new InvalidArgumentException(sprintf("There isn't any task with the ID: %s.", $id));
        }

        return $id;
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
            The <info>%command.name%</info> command updates tasks and saves them:

            <info>php %command.full_name%</info> <comment>--title="New title" --description="New description" --dueDate=2023-10-10 --completed</comment>

            If you omit the id or all options, the command will ask you to
            provide the missing values:

              # command will ask you for all data that could be changed
              <info>php %command.full_name%</info> <comment>id</comment>

              # command will ask you for the ID and all data that could be changed
              <info>php %command.full_name%</info> 

              # command will ask you for the ID
              <info>php %command.full_name% <comment>--title="New title"</comment></info>
            
            The due date can't be earlier than today and had to be in this format: Y-m-d

            HELP;
    }
}