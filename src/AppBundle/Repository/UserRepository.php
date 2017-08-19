<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Workshop;

/**
 * Custom repository for user entity.
 *
 * @subpackage Repository
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class UserRepository extends EntityRepository
{
	/**
     * Counts workshop users without whole selection.
     * 
     * @param AppBundle\Entity\Workshop $workshop
     *
     * @return int
     */
    public function getUserCountByWorkshop(Workshop $workshop)
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT COUNT(u.id)
                FROM AppBundle:User u
                INNER JOIN u.workshop w
                WHERE w.id = :workshop
            ')
            ->setParameter('workshop', $workshop->getId());
            
        return (int)$query->getSingleScalarResult();
    }
}