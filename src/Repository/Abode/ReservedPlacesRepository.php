<?php

namespace App\Repository\Abode;

use Doctrine\ORM\EntityRepository;

class ReservedPlacesRepository extends EntityRepository
{
    /**
     * Return array with elements like [<room_type_id> => <count of reserved places>]
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    public function getReservedPlacesInfo()
    {
        $conn = $this->getEntityManager()->getConnection();

        $stmt = $conn->prepare("
            SELECT
              art.id as room_type_id,
              COALESCE(SUM(arp.count), 0) as reserved_places
            FROM abode.room_type art
              LEFT JOIN abode.reserved_places arp ON art.id = arp.room_type_id
            GROUP BY art.id
        ");

        $stmt->execute();
        $result = [];

        foreach ($stmt->fetchAll() as $item) {
            $result[$item['room_type_id']] = $item['reserved_places'];
        };

        return $result;
    }
}