<?php

namespace AppBundle\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Workshop;

/**
 * Custom repository for user entity.
 *
 * @subpackage Repository
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class UserRepository extends EntityRepository implements UserLoaderInterface
{
    /**
     * Implements custom query logic to get username ignoring case sensivity.
     * @see https://symfony.com/doc/current/security/entity_provider.html
     * 
     * @param string $username
     * @return AppBundle\Entity\User|null
     */
    public function loadUserByUsername($username)
    {
        $username = mb_strtolower($username);
        $query = $this->createQueryBuilder('u')
            ->where('LOWER(u.username) = :username')
            ->setParameter('username', $username)
            ->getQuery();
        
        return $query->getOneOrNullResult();
    }

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