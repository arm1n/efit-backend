<?php

namespace AppBundle\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Workshop;
use AppBundle\Entity\User;
use AppBundle\Entity\Task;

/**
 * Custom repository for result entity.
 *
 * @subpackage Repository
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class ResultRepository extends EntityRepository
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
    public function getOpenResultsByTaskAndUser(Task $task, User $user)
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT r, t
            FROM AppBundle:Result r
            INNER JOIN r.task t
            INNER JOIN r.user u
            WHERE r.user = :user
            AND t.block = :block
            AND r.isPending = 0
            AND r.inBlock = 0
            GROUP BY r.id, t.id
        ')
        ->setParameter('user', $user->getId())
        ->setParameter('block', $task->getBlock());

        return $query->getResult();
    }

    /**
     * Fetches all results by `User` id and `Task` block,
     * which have `isPending` flag currently set false.
     * 
     * @param AppBundle\Entity\User $user
     * @param AppBundle\Entity\Task $task
     * 
     * @return bool
     */
    public function hasResultByTaskAndUser(Task $task, User $user)
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT COUNT(r.id)
            FROM AppBundle:Result r
            INNER JOIN r.task t
            INNER JOIN r.user u
            WHERE r.user = :user
            AND r.task = :task
            AND r.isPending = 0
        ')
        ->setParameter('user', $user->getId())
        ->setParameter('task', $task->getId());

        return (int)$query->getSingleScalarResult() > 0;
    }

    /**
     * Counts distinct results by `User` from `Workshop`,
     * which have `isPending` flag currently set false.
     * 
     * @param AppBundle\Entity\Workshop $workshop
     * 
     * @return array
     */
    public function getResultsByWorkshop(Workshop $workshop)
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT COUNT(r.id) results, t.id
            FROM AppBundle:Result r
            INNER JOIN r.task t
            WHERE t.workshop = :workshop
            AND r.isPending = 0
            GROUP BY t.id
        ')
        ->setParameter('workshop', $workshop->getId());

        return $query->getScalarResult();
    }
}