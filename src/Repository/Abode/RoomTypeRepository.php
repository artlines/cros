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
     * @param null $room_type_id
     * @return mixed[]
     */
    public function getSummaryInformation($room_type_id = null)
    {
        $conn = $this->getEntityManager()->getConnection();

        $where = $room_type_id ? "WHERE rt.id = $room_type_id" : "";

        try {
            $stmt = $conn->prepare("
                SELECT
                  rt.id AS room_type_id,
                  rt.title AS room_type_title,
                  rt.max_places as room_type_max_places,
                  rt.cost as room_type_cost,
                  rt.description as room_type_description,
                  COUNT(DISTINCT cm.id) AS busy,
                  COUNT(DISTINCT p_cm.id) AS populated,
                  COALESCE(rp.reserved, 0) as reserved,
                  COUNT(DISTINCT r.id)*rt.max_places AS total,
                  COUNT(DISTINCT r.id) FILTER (
                    WHERE p_r.id IS NULL
                  ) as free_rooms,
                  COUNT(DISTINCT r.id) as total_rooms
                FROM abode.room_type rt
                  LEFT JOIN abode.room r ON rt.id = r.type_id
                  LEFT JOIN participating.conference_member cm ON rt.id = cm.room_type_id
                  LEFT JOIN abode.place p_cm ON cm.id = p_cm.conference_member_id
                  LEFT JOIN abode.place p_r ON r.id = p_r.room_id
                  LEFT JOIN (
                    SELECT
                      arp.room_type_id,
                      SUM(arp.count) as reserved
                    FROM abode.reserved_places arp
                    GROUP BY arp.room_type_id
                  ) rp ON rp.room_type_id = rt.id
                $where
                GROUP BY rt.id, rp.reserved
            ");
        } catch (\Exception $e) {
            return null;
        }

        $stmt->execute();
        $result = $stmt->fetchAll();

        if ($room_type_id && !empty($result)) {
            $result = $result[0];
        }

        return empty($result) ? null : $result;
    }
}