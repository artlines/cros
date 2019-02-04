<?php

namespace App\Repository;

use AppBundle\Entity\ManagerGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;

/**
 * OrganizationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrganizationRepository extends EntityRepository implements UserLoaderInterface
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

    /**
     * Find members of conference
     *
     * @param string $conf
     * @param bool $not_hidden_only
     * @param boolean|array $mangr
     * @return array The entities.
     */
    public function findAllByConference($conf, $not_hidden_only = false, $mangr = false){
        $hidden_array = "0, 1";
        if($not_hidden_only){
            $hidden_array = "0";
        }
        if($mangr){
        }
        else{
            $mq = $this->getEntityManager()
                ->createQuery('
                SELECT m FROM App:ManagerGroup m
                ');
            $mgs = $mq->getResult();

            /** @var ManagerGroup $mg */
            foreach ($mgs as $mg) {
                $mangr[] = $mg->getId();
            }
        }
        $query = $this->getEntityManager()
            ->createQuery('
            SELECT o, m, otc, u, utoas, ai, c, t FROM App:Organization o
            LEFT JOIN o.managers m
            JOIN o.otc otc
            LEFT JOIN o.users u
            LEFT JOIN u.utoas utoas
            LEFT JOIN utoas.apartament ai
            JOIN otc.conference c
            JOIN o.txtstatus t
            WHERE c.id = :conf AND o.isActive = 1 AND otc.conferenceId = :conf AND ( o.hidden IN (:hidden) OR o.hidden IS NULL) AND (o.manager IN (:mangr) OR o.id IN (1,378)) ORDER BY o.id
            ')->setParameter('conf', $conf)->setParameter('hidden', $hidden_array)->setParameter('mangr', $mangr);
        try{
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e){
            return null;
        }
    }

    /**
     * Find members of conference wo not in conf
     *
     * @param string $conf
     * @param bool $not_hidden_only
     * @return array The entities.
     */
    public function findAllByConferenceWoNot($conf, $not_hidden_only = false){
        $hidden_array = "0, 1";
        if($not_hidden_only){
            $hidden_array = "0";
        }
        $query = $this->getEntityManager()
            ->createQuery('
            SELECT o, otc, u, utoas, ai, utc, c, t FROM App:Organization o
            JOIN o.otc otc
            JOIN o.users u
            LEFT JOIN u.utoas utoas
            LEFT JOIN utoas.apartament ai
            JOIN u.utocs utc
            JOIN otc.conference c
            JOIN o.txtstatus t
            WHERE c.id = :conf AND o.isActive = 1 AND otc.conferenceId = :conf AND ( o.hidden IN (:hidden) OR o.hidden IS NULL) ORDER BY o.id
            ')->setParameter('conf', $conf)->setParameter('hidden', $hidden_array);
        try{
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e){
            return null;
        }
    }

    /**
     * Find conf with user in conf
     *
     * @param array $ids
     * @param integer $conf_id
     * @param integer|bool $mangrid
     * @param bool $approved
     *
     * @return object|array|null
     */
    public function findByIdsWithConfUser($ids, $conf_id, $mangrid = false, $approved = false, $show_empty = false)
    {
        $where = '';

        if ($approved)
        {
            $where .= ' AND o.status IN (2,3,4) AND otc.paid = 1';
        }

        if ($mangrid)
        {
            $where .= ' AND o.manager = '.$mangrid;
        }



        $query = $this->getEntityManager()
            ->createQuery('
            SELECT o, u, otc, s, utc, uta, ai, a, f1, f2, f3, f4, f5, t1, t2, t3, t4, t5 FROM App:Organization o
            LEFT JOIN o.users u
            JOIN o.otc otc
            JOIN o.txtstatus s
            LEFT JOIN u.utocs utc
            LEFT JOIN u.utoas uta
            LEFT JOIN uta.apartament ai
            LEFT JOIN ai.apartament a
            LEFT JOIN ai.flats1 f1
            LEFT JOIN ai.flats2 f2
            LEFT JOIN ai.flats3 f3
            LEFT JOIN ai.flats4 f4
            LEFT JOIN ai.flats5 f5
            LEFT JOIN f1.type t1
            LEFT JOIN f2.type t2
            LEFT JOIN f3.type t3
            LEFT JOIN f4.type t4
            LEFT JOIN f5.type t5
            WHERE o.id IN (:ids) 
                '. (!$show_empty ? 'AND utc.conferenceId = :conf_id' : '') . ' 
                AND otc.conferenceId = :conf_id 
            '.$where.'
            ')->setParameter('ids', $ids)->setParameter('conf_id', $conf_id);
        try {
            return $query->getResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }
    /**
     * @param
     * @return array
     * We return the list of participants only those who have already settled in numbers
     */
    public function findByIdsOrganizationApproved($params = array())
    {
        $conn = $this->getEntityManager()->getConnection();

        $select = isset($params['count']) ? 'count(*)' : 'name, org.id, city, title';

        $sql = "SELECT
	              $select
                FROM
	              organization org
                inner JOIN organization_status ss ON
	              org.status = ss.id
                where
                      org.id IN(
                    SELECT
                      organization_id
                    FROM
                      `user` us
                    left JOIN user_to_apartament apar ON
                      us.id = apar.user_id
                    WHERE
                      approved = 1
                ) 
                AND hidden = 0
                ORDER BY priority DESC, name ";

        if (isset($params['limit'])) {
            $limit = intval($params['limit']);
            $offset = (isset($params['offset'])) ? intval($params['offset']) : 0;
            $sql .= " LIMIT $offset,$limit";
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $res = $stmt->fetchAll();

        if (isset($params['count'])) {
            return $res[0]['count(*)'];
        }

        return $res;
    }

    /**
     * возвращает названия организаций с заданным ИНН/КПП, окончательно зарегистрированных в конференции текущего года
     * @param string $inn - ИНН организации
     * @param string $kpp - КПП организации
     * @return array
     */
    public function findByInnKppIsFinish($inn, $kpp)
    {
        $conn = $this->getEntityManager()->getConnection();
        $params=['inn'=>$inn,'kpp'=>$kpp,'y'=>date('Y')];

        $sql = "SELECT
	              o.name as name
                FROM
	              participating.conference_organization co
                inner JOIN participating.organization o ON co.organization_id = o.id
                inner JOIN public.conference c ON co.conference_id = c.id
                where
                  o.inn = :inn AND 
                  o.kpp = :kpp AND 
                  co.finish = true AND 
                  c.year = :y";

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}