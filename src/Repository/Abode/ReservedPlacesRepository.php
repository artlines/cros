<?php

namespace App\Repository\Abode;

use Doctrine\ORM\EntityRepository;

class ReservedPlacesRepository extends EntityRepository
{
    /**
     * Return array with elements like [<room_type_id> => <count of reserved places>]
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param integer|null $housing_id
     * @return mixed[]
     */
    public function getReservedInfo($housing_id = null)
    {
        $conn = $this->getEntityManager()->getConnection();

        $where = $housing_id ? "WHERE ah.id = $housing_id" : "";

        try {
            $stmt = $conn->prepare("
                SELECT
                  arp.room_type_id,
                  SUM(arp.count) as reserved_places
                FROM abode.reserved_places arp
                  LEFT JOIN abode.housing ah ON arp.housing_id = ah.id
                $where
                GROUP BY arp.room_type_id
            ");
        } catch (\Exception $e) {
            return [];
        }

        $stmt->execute();

        $result = [];
        foreach ($stmt->fetchAll() as $item) {
            $result[$item['room_type_id']] = $item['reserved_places'];
        };

        return $result;
    }
}