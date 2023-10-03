<?php

namespace Console\App\Utils;

use Console\App\Entity\EntityInterface;

/**
 * Interface for the method to manage and persist entities in different format files
 */
interface EntityManagerInterface
{
    /**
     * Method to add or update the entity
     *
     * @param EntityInterface $entity
     * @return bool
     */
    public function persist(EntityInterface $entity):bool;

    /**
     * Method to delete the entity
     *
     * @param EntityInterface $entity
     * @return bool
     */
    public function delete(EntityInterface $entity):bool;

}