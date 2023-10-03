<?php

namespace Console\App\Entity;

/**
 * Enum to manage the states of the Tasks.
 */
enum TaskState: String
{
    case PENDING = "pending";
    case COMPLETED = "completed";
}