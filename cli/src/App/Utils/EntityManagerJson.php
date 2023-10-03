<?php

namespace Console\App\Utils;

use Console\App\Entity\EntityInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Uid\Uuid;

/**
 * This class implements the EntityManager to manage the entities in JSON files
 */
class EntityManagerJson implements EntityManagerInterface
{
    /**
     * Variable that contains the route where we are going to store the entities
     *
     * @var string
     */
    private string $path = __DIR__ . '/../../../files/json/';

    /**
     * Variable that contains the extension of the file where we are going to store the entities
     *
     * @var string
     */
    private string $extension = '.json';

    /**
     * Method to store the entity in the JSON file
     *
     * @param EntityInterface $entity
     * @return bool
     * @throws ReflectionException
     */
    public function persist(EntityInterface $entity): bool
    {
        $entityName = get_class($entity);
        $entities = $this->getAllRawData($entityName);
        $id = $entity->getId() ? $entity->getId()->__toString() : 0;

        //If the id of the entity doesn't exist, it means that it's a creation
        if (empty($entities[$id])) {
            $this->setID($entity);
            $entities[$entity->getId()->__toString()] = $entity;
        //If the id of the entity doesn't exist, it means that it's an update
        } else {
            $entities[$id] = $entity;
        }

        return file_put_contents($this->getEntityPath($entityName), json_encode($entities));
    }

    /**
     * Method to delete the entity from the JSON file
     *
     * @param EntityInterface $entity
     * @return bool
     * @throws ReflectionException
     */
    public function delete(EntityInterface $entity): bool
    {
        $entityName = get_class($entity);
        $entities = $this->getAllRawData($entityName);
        $id = $entity->getId()->__toString();

        if (!empty($entities[$id])) {
            unset($entities[$id]);
            return file_put_contents($this->getEntityPath($entityName), json_encode($entities));
        }

        return true;
    }

    /**
     * Method to recover all the data in the JSON file
     *
     * @param string $entityName
     * @return array
     * @throws ReflectionException
     */
    public function getAllRawData(string $entityName): array
    {
        return json_decode(file_get_contents($this->getEntityPath($entityName)), true) ?: [];
    }

    /**
     * With this method we set the ID in the entity when we create a new one
     *
     * @param EntityInterface $entity
     * @return void
     * @throws ReflectionException
     */
    private function setID(EntityInterface $entity): void
    {
        $reflectionClass = new ReflectionClass(get_class($entity));
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($entity, Uuid::v4());
    }

    /**
     * Method to retrieve the path of the entity storage
     *
     * @param string $className
     * @return string
     * @throws ReflectionException
     */
    private function getEntityPath(string $className): string
    {
        $reflectionClass = new ReflectionClass($className);
        return $this->path . strtolower($reflectionClass->getShortName()) . $this->extension;
    }
}