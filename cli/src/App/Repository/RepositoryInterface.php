<?php

namespace Console\App\Repository;

use Console\App\Entity\EntityInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Interface for our repositories.
 */
interface RepositoryInterface
{
    /**
     * Method to find and load an entity by ID
     *
     * @param Uuid $id
     * @return EntityInterface|bool
     */
    public function findOne(Uuid $id): EntityInterface|bool;

    /**
     * Method to find and load all the entities
     *
     * @return array
     */
    public function findAll(): array;
}