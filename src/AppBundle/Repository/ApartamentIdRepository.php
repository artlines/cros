<?php

namespace AppBundle\Repository;

/**
 * ApartamentIdRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ApartamentIdRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAllWithUser($conf_id){
        $query = $this->getEntityManager()
            ->createQuery('
            SELECT ai, a, utoa, u, o FROM AppBundle:ApartamentId ai
            JOIN ai.apartament a
            JOIN ai.atoais utoa
            JOIN utoa.user u
            JOIN u.organization o
            WHERE a.conferenceId = :conf
            ')->setParameter('conf', $conf_id);
        try{
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e){
            return null;
        }
    }


    public function findAllWithUserNotInFlat($conf_id){
        $query = $this->getEntityManager()
            ->createQuery('
            SELECT ai, a, utoa, u, o, m, f1, f2, f3, f4, f5 FROM AppBundle:ApartamentId ai
            JOIN ai.apartament a
            LEFT JOIN ai.flats1 f1
            LEFT JOIN ai.flats2 f2
            LEFT JOIN ai.flats3 f3
            LEFT JOIN ai.flats4 f4
            LEFT JOIN ai.flats5 f5
            LEFT JOIN ai.atoais utoa
            LEFT JOIN utoa.user u
            LEFT JOIN u.organization o
            LEFT JOIN o.managers m
            WHERE a.conferenceId = :conf AND utoa.approved = 1
            ')->setParameter('conf', $conf_id);
        try{
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e){
            return null;
        }
    }
}
