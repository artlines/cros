<?php

namespace AppBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * LectureRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LectureRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->findBy(array(), array('date' => 'ASC', 'startTime' => 'ASC'));
    }

	
	public function findByNotInIds($arrayIDs)
	{
		$query = $this->getEntityManager()
				->createQuery('SELECT l from AppBundle:Lecture l
								WHERE l.id NOT IN (:arrayids)')
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

    /**
     * @return array
     */
	public function findByHalls()
    {
        return $this->createQueryBuilder('l')
            ->select('l.hall')
            ->distinct()
            ->getQuery()->getArrayResult();
    }

}
