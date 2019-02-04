<?php
/**
 * Created by PhpStorm.
 * User: esuzev
 * Date: 04.02.19
 * Time: 11:10
 */

namespace App\Repository\Abode;


use App\Entity\Conference;
use App\Entity\Participating\ConferenceMember;
use Doctrine\ORM\EntityRepository;

class RoomTypeRepository  extends EntityRepository
{

    function findAllFreeForConference($conference_id){
        return $this
            ->createQueryBuilder('rt')
            ->leftJoin(
                ConferenceMember::class,
                'cm',
                "WITH",
                'cm.conference=:conference_id AND cm.roomType=rt.id'
            )
            ->addSelect('count(rt.id)')
            ->addGroupBy('rt.id')
            ->setParameters([
                'conference_id' => $conference_id,
            ])
            ->getQuery()
            ->getResult();
    }

}