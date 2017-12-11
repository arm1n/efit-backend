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
class TicketRepository extends EntityRepository
{
	/**
     * Fetches all results by `User` id and `Task` block,
     * which have `inRound` still set to false (default).
     * 
     * @param AppBundle\Entity\User $user
     * @param AppBundle\Entity\Task $task
     * 
     * @return AppBundle\Entity\Result[]
     */
    public function getTicketsByWorkshop(Workshop $workshop)
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT t.id, u.username
            FROM AppBundle:Ticket t
            INNER JOIN t.user u
            INNER JOIN u.workshop w
            WHERE w.id = :workshop
        ')
        ->setParameter('workshop', $workshop->getId());

        return $query->getResult();
    }
}