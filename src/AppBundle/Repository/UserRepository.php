<?php

namespace AppBundle\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Mapping AS ORM;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository implements UserLoaderInterface
{
    /**
     * @param string $username
     * @return object|null
     */
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllByUsers($users = array(), $ap = 0)
    {
        $ids_array = array();
        foreach ($users as $user) {
            $ids_array[$user->getUserId()] = $user->getUserId();
        }
        ksort($ids_array);

        $query = $this->getEntityManager()
            ->createQuery(
                'SELECT u, o, uta, ai FROM AppBundle:User u
                LEFT JOIN u.organization o
                LEFT JOIN u.utoas uta
                LEFT JOIN uta.apartament ai
                LEFT JOIN ai.apartament a
                WHERE u.id in(:ids) AND (uta.approved = :approved OR uta.approved IS NULL) ORDER BY u.id ASC'
            )->setParameter('ids', $ids_array)->setParameter('approved', $ap);

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }

    }

    public function findManagers($conf_id, $year)
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT u FROM AppBundle:User u
                WHERE u.roles LIKE :role
            ')->setParameter('role', "%ROLE_MANAGER%");//->setParameter('conf', $conf_id)->setParameter('year', $year);

        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    /** Find by gender
     * @param string $gender
     * @return object|null
     */
    public function findGender($gender = 'female')
    {
        if ($gender == 'female') {
            $query = $this->getEntityManager()
                ->createQuery('
                    SELECT u, utocs, o, uta, a FROM AppBundle:User u
                    JOIN u.utocs utocs
                    JOIN u.organization o
                    LEFT JOIN u.utoas uta
                    LEFT JOIN uta.apartament a
                    WHERE u.female = 1
                    ORDER BY a.id
                ');
            try {
                return $query->getResult();
            } catch (\Doctrine\ORM\NoResultException $e) {
                return null;
            }
        }
    }

    /**
     * Finder for speakers
     * @param string $find
     * @return object|null
     */
    public function findUser($find)
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT u, o FROM AppBundle:User u
                LEFT JOIN u.organization o
                WHERE u.firstName IN (:find)
                AND u.lastName IN (:find)
            ')->setParameter('find', $find);
        try {
            return $query->setMaxResults(5)->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    /**
     * Search user
     * @param string $string
     * @return object|null
     */
    public function search($string)
    {
        $strings = explode(' ', $string);
        if (count($strings) != 2) {
            $query = $this->getEntityManager()
                ->createQuery('
                SELECT u, o FROM AppBundle:User u
                LEFT JOIN u.organization o
                WHERE u.firstName IN (:strings)
                OR u.lastName IN (:strings)
                OR u.middleName IN (:strings)
                OR o.name IN (:strings)
                OR o.name LIKE :string
            ')->setParameter('strings', $strings)->setParameter('string', '%' . $string . '%');
        } else {
            $query = $this->getEntityManager()
                ->createQuery('
                    SELECT u, o FROM AppBundle:User u
                    LEFT JOIN u.organization o
                    WHERE (u.firstName IN (:strings)
                    AND u.lastName IN (:strings))
                    OR (u.firstName IN (:strings)
                    AND u.middleName IN (:strings))
                    OR (o.name LIKE :string)
                ')->setParameter('strings', $strings)->setParameter('string', '%' . $string . '%');
        }

        try {
            return $query->setMaxResults(5)->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    /**
     * Search user
     * @param string $string
     * @param int $offset
     * @param int $limit
     * @return object|null
     */
    public function easySearchUser($string, $offset, $limit)
    {
        $query = $this->getEntityManager()
            ->createQuery('
                    SELECT u FROM AppBundle:User u
                    WHERE (u.firstName LIKE :string  
                    OR u.middleName LIKE :string
                    OR u.username LIKE :string
                    OR u.email LIKE :string
                    OR u.nickname LIKE :string
                    OR u.lastName LIKE :string
                    )
                ')
            ->setParameter('string', '%' . $string . '%')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        $paginator = new Paginator($query, $fetchJoinCollection = true);
        $c = count($paginator);
        $search = new \stdClass();
        $search->query = $query->getResult();
        $search->count = $c;
        try {
            return $search;
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
}
