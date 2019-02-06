<?php

namespace App\Repository;

/**
 * ConferenceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ConferenceRepository extends \Doctrine\ORM\EntityRepository
{
    public function findWithInfo(){

        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT itc, i, c FROM App:Conference c
                LEFT JOIN c.infotoconfs itc
                LEFT JOIN itc.info i
                ORDER BY c.year DESC'
            );

        try{
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e){
            return null;
        }
    }

    public function findWithArchiveOnly(){

        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT itc, i, c FROM App:Conference c
                LEFT JOIN c.infotoconfs itc
                LEFT JOIN itc.info i
                WHERE i.content IS NOT NULL AND i.alias = :alias ORDER BY c.year DESC'
            )->setParameter('alias', 'result');

        try{
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e){
            return null;
        }
    }

    public function findOpen()
    {
        return $this
            ->createQueryBuilder('c')
            ->andWhere('c.registrationStart <= :registrationStart')
            ->andWhere('c.registrationFinish >= :registrationFinish')
            ->setParameters([
                'registrationStart' => new \DateTime(),
                'registrationFinish' => new \DateTime()
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
