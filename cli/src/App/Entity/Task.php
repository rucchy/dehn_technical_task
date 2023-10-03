<?php

namespace Console\App\Entity;

use DateTime;
use DateTimeZone;
use Exception;
use JsonSerializable;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

/**
 * This is our entity to manage the tasks.
 */
class Task implements EntityInterface, JsonSerializable
{

    /**
     * Variable that contains the name of the file where we are going to store the tasks
     *
     * @var string
     */
    private string $nameFileToSave = "tasks";
    /**
     * @var Uuid
     */
    private Uuid $id;

    /**
     * @var String
     */
    private string $title;

    /**
     * @var String
     */
    private string $description;

    /**
     * @var DateTime
     */
    private DateTime $due_date;

    /**
     * @var TaskState
     */
    private TaskState $status;

    /**
     * The construct of the task.
     * We use to create new task or "load" the data (en form of array) from the file.
     *
     * Maybe other solution is split this functionality in the construct for a new Task and createFromArray to
     * load a Task.
     * Also, this construct be done in an abstract Entity and extend from that because all the entities in the system
     * has to do the same thing.
     *
     * @param array|null $fields
     * @throws Exception
     */
    public function __construct(?array $fields = null)
    {
        //If there not fields means that is a new Task so the status is Pending always
        if (empty($fields)) {
            $this->status = TaskState::PENDING;
        } else {
            foreach ($fields as $key => $field) {
                //We store the data in a JSON file, so we need to convert into the types.
                switch ($key) {
                    case 'status':
                        $this->$key = TaskState::from($field);
                        break;
                    case 'id':
                        $this->$key = UuidV4::fromString($field);
                        break;
                    case 'due_date':
                        $this->$key = new DateTime($field['date'], new DateTimeZone($field['timezone']));
                        break;
                    default:
                        $this->$key = $field;
                }
            }
        }
    }

    /**
     * @return ?Uuid
     */
    public function getId(): ?Uuid
    {
        return !empty($this->id) ? $this->id : null;
    }

    /**
     * @return String
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param String $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return String
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param String $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return DateTime
     */
    public function getDueDate(): DateTime
    {
        return $this->due_date;
    }

    /**
     * @param DateTime $due_date
     */
    public function setDueDate(DateTime $due_date): void
    {
        $this->due_date = $due_date;
    }

    /**
     * @return TaskState
     */
    public function getStatus(): TaskState
    {
        return $this->status;
    }

    /**
     * @param TaskState $status
     */
    public function setStatus(TaskState $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getNameFileToSave(): string
    {
        return $this->nameFileToSave;
    }

    /**
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date,
            'status' => $this->status->value,
        ];
    }
}