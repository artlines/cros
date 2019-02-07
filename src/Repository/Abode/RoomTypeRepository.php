<?php
/**
 * Created by PhpStorm.
 * User: esuzev
 * Date: 04.02.19
 * Time: 11:10
 */

namespace App\Repository\Abode;


use App\Entity\Abode\Room;
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
            ->leftJoin(
                Room::class,
                'r',
                "WITH",
                'r.type=rt.id'
            )
            ->addSelect('count(DISTINCT cm.id)')
            ->addSelect('count(DISTINCT r.id)')
            ->addGroupBy('rt.id')
            ->setParameters([
                'conference_id' => $conference_id,
            ])
            ->orderBy('rt.title')
            ->getQuery()
            ->getResult();
    }

}