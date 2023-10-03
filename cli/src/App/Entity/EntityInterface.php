<?php

namespace Console\App\Entity;

use Symfony\Component\Uid\Uuid;

/**
 * Interface for our entities.
 * In this way we have the security that our entities implements those methods that are necessary for the EntityManager
 */
interface EntityInterface
{
    /**
     * This method is required to know the name of the file where we are going to save the entities
     *
     * @return string
     */
    public function getNameFileToSave(): string;

    /**
     * This method is required to return the ID of the entities
     *
     * @return Uuid|null
     */
    public function getId(): ?Uuid;
}