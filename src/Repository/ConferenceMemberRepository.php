<?php
/**
 * Created by PhpStorm.
 * User: esuzev
 * Date: 04.02.19
 * Time: 9:19
 */

namespace App\Repository;


use App\Entity\Participating\User;
use Doctrine\ORM\EntityRepository;

class ConferenceMemberRepository extends EntityRepository
{

    /**
     * @param $conference_id
     * @param $email
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findConferenceMemberByEmail($conference_id, $email)
    {
        return $this
            ->createQueryBuilder('cm')
            ->leftJoin(User::class, 'u',"WITH",'cm.user=u.id')
            ->where('cm.conference = :conference_id')
            ->andWhere('u.email = :email')
            ->setParameters([
                'conference_id' => $conference_id,
                'email' => $email,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}