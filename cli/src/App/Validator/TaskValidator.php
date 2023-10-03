<?php

namespace Console\App\Validator;

use DateTime;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Uid\UuidV4;

/**
 * Class to validate the data of a Task
 */
class TaskValidator
{
    /**
     * Function to validate the ID for a task
     *
     * @param string|null $id
     * @return string
     */
    public function validateID(?string $id): string
    {
        if (empty($id)) {
            throw new InvalidArgumentException('The ID can not be empty.');
        }

        if (!UuidV4::isValid($id)) {
            throw new InvalidArgumentException("Invalid UUID.");
        }

        return $id;
    }

    /**
     * Function to validate the title of a task
     *
     * @param string|null $title
     * @return string
     */
    public function validateTitle(?string $title): string
    {
        if (empty($title)) {
            throw new InvalidArgumentException('The title can not be empty.');
        }

        return $title;
    }

    /**
     * Function to validate the description of a task
     *
     * @param string|null $description
     * @return string
     */
    public function validateDescription(?string $description): string
    {
        if (empty($description)) {
            throw new InvalidArgumentException('The description can not be empty.');
        }

        return $description;
    }

    /**
     * Function to validate the due date of a task
     *
     * @param string|null $date
     * @return string
     * @throws Exception
     */
    public function validateDueDate(?string $date): string
    {
        if (empty($date)) {
            throw new InvalidArgumentException('The due date can not be empty.');
        }

        $format = "Y-m-d";
        $dueDate = DateTime::createFromFormat($format, $date);
        if (!$dueDate || $dueDate->format($format) !== $date) {
            throw new InvalidArgumentException('The format of the due date is wrong. It should be Y-m-d.');
        }

        if ($dueDate < new DateTime(date($format))) {
            throw new InvalidArgumentException('The due date can not be earlier than today.');
        }


        return $date;
    }
}