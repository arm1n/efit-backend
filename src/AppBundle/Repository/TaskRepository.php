<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Workshop;

/**
 * Custom repository for task entity.
 *
 * @subpackage Repository
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class TaskRepository extends EntityRepository
{
	/**
     * Retrieves current `isActive` flag and `type` from workshop's tasks.
     * 
     * @param AppBundle\Entity\Workshop $workshop
     *
     * @return array
     */
    public function getTaskStatesByWorkshop(Workshop $workshop)
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT t.isActive, t.updatedAt, t.type
                FROM AppBundle:Task t
                WHERE t.workshop = :workshop
            ')
            ->setParameter('workshop', $workshop->getId());
            
        return $query->getScalarResult();
    }
}