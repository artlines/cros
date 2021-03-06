<?php

namespace App\Repository;

/**
 * UserToConfRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserToConfRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param string $conference_id
     * @param string $order
     * @return object|array|null
     */
    public function findByConfWithPost($conference_id, $order = 'o.id')
    {
        $query = $this->getEntityManager()
            ->createQuery('
            SELECT utc, u, o FROM App:UserToConf utc
            LEFT JOIN utc.user u
            LEFT JOIN u.organization o
            WHERE utc.conferenceId = :conf
            ORDER BY '.$order.' ASC
            ')->setParameter('conf', $conference_id);
        try{
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e){
            return null;
        }
    }

}
