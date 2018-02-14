<?php

namespace AppBundle\Repository;

/**
 * ApartamentPairRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ApartamentPairRepository extends \Doctrine\ORM\EntityRepository
{
    public function findFull($conf, $without_corpuses = false){
        $wo_corp = '';
        if($without_corpuses){
            $wo_corp = ' AND s1.id IS NULL AND s2.id IS NULL AND s3.id IS NULL AND s4.id IS NULL';
        }
        $query = $this->getEntityManager()
            ->createQuery('
            SELECT ap, a, aty, f, ai1, ai2, ai3, ai4, ai5, ua1, ua2, ua3, ua4, ua5, u1, u2, u3, u4, u5, o1, o2, o3, o4, o5, s1, s2, s3, s4 FROM AppBundle:ApartamentPair ap
            JOIN ap.apartaments a
            LEFT JOIN ap.apartamentTypes aty
            LEFT JOIN aty.flats f
            LEFT JOIN f.realroom1 ai1
            LEFT JOIN f.realroom2 ai2
            LEFT JOIN f.realroom3 ai3
            LEFT JOIN f.realroom4 ai4
            LEFT JOIN f.realroom5 ai5
            LEFT JOIN f.stages1 s1
            LEFT JOIN f.stages2 s2
            LEFT JOIN f.stages3 s3
            LEFT JOIN f.stages4 s4
            LEFT JOIN ai1.atoais ua1
            LEFT JOIN ai2.atoais ua2
            LEFT JOIN ai3.atoais ua3
            LEFT JOIN ai4.atoais ua4
            LEFT JOIN ai5.atoais ua5
            LEFT JOIN ua1.user u1
            LEFT JOIN ua2.user u2
            LEFT JOIN ua3.user u3
            LEFT JOIN ua4.user u4
            LEFT JOIN ua5.user u5
            LEFT JOIN u1.organization o1
            LEFT JOIN u2.organization o2
            LEFT JOIN u3.organization o3
            LEFT JOIN u4.organization o4
            LEFT JOIN u5.organization o5
            WHERE a.conferenceId = :conf '.$wo_corp.'
            ORDER BY ap.id ASC
            ')->setParameter('conf', $conf);
        try{
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e){
            return null;
        }
    }

    public function findFullWoCorpus($conf){
        $query = $this->getEntityManager()
            ->createQuery('
            SELECT ap, a, aty, f, ai1, ai2, ai3, ai4, ai5, ua1, ua2, ua3, ua4, ua5, u1, u2, u3, u4, u5, o1, o2, o3, o4, o5, s1, s2, s3, s4 FROM AppBundle:ApartamentPair ap
            JOIN ap.apartaments a
            LEFT JOIN ap.apartamentTypes aty
            LEFT JOIN aty.flats f
            LEFT JOIN f.realroom1 ai1
            LEFT JOIN f.realroom2 ai2
            LEFT JOIN f.realroom3 ai3
            LEFT JOIN f.realroom4 ai4
            LEFT JOIN f.realroom5 ai5
            LEFT JOIN f.stages1 s1
            LEFT JOIN f.stages2 s2
            LEFT JOIN f.stages3 s3
            LEFT JOIN f.stages4 s4
            LEFT JOIN ai1.atoais ua1
            LEFT JOIN ai2.atoais ua2
            LEFT JOIN ai3.atoais ua3
            LEFT JOIN ai4.atoais ua4
            LEFT JOIN ai5.atoais ua5
            LEFT JOIN ua1.user u1
            LEFT JOIN ua2.user u2
            LEFT JOIN ua3.user u3
            LEFT JOIN ua4.user u4
            LEFT JOIN ua5.user u5
            LEFT JOIN u1.organization o1
            LEFT JOIN u2.organization o2
            LEFT JOIN u3.organization o3
            LEFT JOIN u4.organization o4
            LEFT JOIN u5.organization o5
            WHERE a.conferenceId = :conf 
            AND ap.id = 1 
            AND aty.id IN(1, 2) 
            AND s1.id IS NULL 
            AND s2.id IS NULL 
            AND s3.id IS NULL 
            AND s4.id IS NULL
            ORDER BY ap.id ASC
            ')->setParameter('conf', $conf);
        try{
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e){
            return null;
        }
    }
}
