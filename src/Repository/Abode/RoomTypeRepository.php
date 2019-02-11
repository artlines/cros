<?php

namespace App\Repository\Abode;

use App\Entity\Abode\Room;
use App\Entity\Conference;
use App\Entity\Participating\ConferenceMember;
use Doctrine\ORM\EntityRepository;

class RoomTypeRepository  extends EntityRepository
{
    public function findAllFreeForConference($conference_id)
    {
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

    /**
     * Return information about settlement by room types
     */
    public function getSummaryInformation()
    {
        $conn = $this->getEntityManager()->getConnection();

        $stmt = $conn->prepare("
            SELECT
              rt.id AS room_type_id,
              rt.title AS room_type_title,
              COUNT(DISTINCT cm.id) AS busy,
              COUNT(DISTINCT p.id) AS populated,
              COUNT(DISTINCT r.id)*rt.max_places AS total
            FROM abode.room_type rt
              LEFT JOIN abode.room r ON rt.id = r.type_id
              LEFT JOIN participating.conference_member cm ON rt.id = cm.room_type_id
              LEFT JOIN abode.place p ON cm.id = p.conference_member_id
            GROUP BY rt.id
        ");

        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;
    }
}