<?php

namespace AppBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use AppBundle\Entity\Workshop;
use AppBundle\Entity\Task;

/**
 * Workshop listener for auto inserting `Task` entities on insert.
 * 
 * http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/events.html#onflush
 *
 * @subpackage EventListener
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class WorkshopListener
{
    /**
     * Handles creation of `Task` entities when a new `Workshop` is persisted.
     * 
     * @param  OnFlushEventArgs $args
     * @return Void
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();
        $metaData = $entityManager->getClassMetadata(Task::class);

        // --- INSERTIONS ---
        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            if (!$entity instanceof Workshop) {
                continue;
            }

            // get all supported types of `Task`
            foreach(Task::getTypes() as $type) {

                // create a new `Task` entity 
                $task = new Task();
                $task->setType($type);

                // connect `Task` and `Workshop`
                $task->setWorkshop($entity);
                $entity->addTask($task);
                
                // persist and compute changeset
                $entityManager->persist($task);
                $unitOfWork->computeChangesets($metaData, $task);
            }
        }

        // --- UPDATES ---
        /*foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof Workshop) {
                continue;
            }
        }*/

        // --- DELETIONS ---
        /*foreach ($unitOfWork->getScheduledEntityDeletions() as $entity) {
            if (!$entity instanceof Workshop) {
                continue;
            }
        }*/
    }
}