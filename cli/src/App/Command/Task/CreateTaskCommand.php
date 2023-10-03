<?php

namespace Console\App\Command\Task;

use Console\App\Command\BaseCommand;
use Console\App\Entity\Task;
use Console\App\Utils\EntityManagerInterface;
use Console\App\Validator\TaskValidator;
use DateTime;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * A console command that creates tasks and stores them.
 *
 * To use this command, open a terminal window, enter into your project
 * directory and execute the following:
 *
 *     $ php bin/console create-task
 *
 */
#[AsCommand(
    name: 'app:create-task',
    description: 'Creates tasks and stores them'
)]
class CreateTaskCommand extends BaseCommand
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TaskValidator          $validator
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
            ->addArgument('title', InputArgument::OPTIONAL, 'The title of the new task')
            ->addArgument('description', InputArgument::OPTIONAL, 'The description of the new task')
            ->addArgument('due-date', InputArgument::OPTIONAL, 'The due date of the new task. The format has to be Y-m-d.');
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

        $this->io->title('Create Task Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console create-task title description 2023-09-30',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
        ]);

        // Ask for the title if it's not defined
        $title = $input->getArgument('title');
        if (null !== $title) {
            $this->io->text(' > <info>Title</info>: ' . $title);
            $this->validator->validateTitle($title);
        } else {
            $title = $this->io->ask('Title', null, $this->validator->validateTitle(...));
            $input->setArgument('title', $title);
        }

        // Ask for the description if it's not defined
        $description = $input->getArgument('description');
        if (null !== $description) {
            $this->io->text(' > <info>Description</info>: ' . $description);
            $this->validator->validateDescription($description);
        } else {
            $description = $this->io->ask('Description', null, $this->validator->validateDescription(...));
            $input->setArgument('description', $description);
        }

        // Ask for the due date if it's not defined
        $dueDate = $input->getArgument('due-date');
        if (null !== $dueDate) {
            $this->io->text(' > <info>Due date</info>: ' . $dueDate);
            $this->validator->validateDueDate($dueDate);
        } else {
            $dueDate = $this->io->ask('Due date (Y-m-d)', null, $this->validator->validateDueDate(...));
            $input->setArgument('due-date', $dueDate);
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
        /** @var string $title */
        $title = $input->getArgument('title');

        /** @var string $description */
        $description = $input->getArgument('description');

        /** @var string $dueDate */
        $dueDate = $input->getArgument('due-date');

        // create the task
        $task = new Task();
        $task->setTitle($title);
        $task->setDescription($description);
        $task->setDueDate(new DateTime($dueDate));
        if ($this->entityManager->persist($task)) {
            $this->io->success(sprintf('The task %s was successfully created with the ID: %s', $task->getTitle(), $task->getId()));
            return Command::SUCCESS;
        }

        $this->io->error("Something goes wrong and the task doesn't create.");
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
            The <info>%command.name%</info> command creates new tasks and saves them:

            <info>php %command.full_name%</info> <comment>title description due-date</comment>

            If you omit any of the three required arguments, the command will ask you to
            provide the missing values:

              # command will ask you for the due-date
              <info>php %command.full_name%</info> <comment>title description</comment>

              # command will ask you for the description and due-date
              <info>php %command.full_name%</info> <comment>title</comment>

              # command will ask you for all arguments
              <info>php %command.full_name%</info>
            
            The due date can't be earlier than today and had to be in this format: Y-m-d

            HELP;
    }
}