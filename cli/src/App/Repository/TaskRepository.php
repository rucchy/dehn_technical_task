<?php

namespace Console\App\Repository;

use Console\App\Entity\Task;
use Console\App\Utils\EntityManagerInterface;
use Exception;
use Symfony\Component\Uid\Uuid;

/**
 * This class is the repository of Tasks.
 * We centralize all the "query" logic here.
 */
class TaskRepository implements RepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Method to find and load a task by ID
     *
     * @param Uuid $id
     * @return Task|bool
     * @throws Exception
     */
    public function findOne(Uuid $id): Task|bool
    {
        $tasks = $this->entityManager->getAllRawData(Task::class);

        if(!empty($tasks[$id->__toString()])){
            return new Task($tasks[$id->__toString()]);
        }
        return false;
    }

    /**
     * Method to find and load all the tasks
     *
     * @return array
     * @throws Exception
     */
    public function findAll(): array
    {
        $result = [];
        $tasks = $this->entityManager->getAllRawData(Task::class);
        foreach($tasks as $task){
            $result[] = new Task($task);
        }
        return $result;
    }
}