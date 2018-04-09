<?php
/**
 * Created by PhpStorm.
 * User: alf1kk
 * Date: 13.03.18
 * Time: 13:44
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class HallRepository extends EntityRepository
{
    public function findByNotInIds($arrayIDs)
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT h from AppBundle:hall h
								WHERE h.id NOT IN (:arrayids)')
            ->setParameter('arrayids', $arrayIDs);

        try
        {
            return $query->getResult();
        }
        catch (\Doctrine\ORM\NoResultException $e)
        {
            return null;
        }
    }
}