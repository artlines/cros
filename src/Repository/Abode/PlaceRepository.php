<?php

namespace App\Repository\Abode;

use App\Entity\Abode\RoomType;
use Doctrine\ORM\EntityRepository;

class PlaceRepository extends EntityRepository
{
    public function getPlacesInfoByType(RoomType $type)
    {
        $conn = $this->getEntityManager()->getConnection();

        $stmt = $conn->prepare("
            SELECT ar.id, app.number as apartment_number, ah.title as housing_title, count(*) as places_count
            FROM abode.place ap
              LEFT JOIN abode.room ar       ON ap.room_id = ar.id
              LEFT JOIN abode.room_type art ON art.id = ar.type_id
              LEFT JOIN abode.apartment app ON app.id = ar.apartment_id
              LEFT JOIN abode.housing ah    ON ah.id = app.housing_id
            WHERE art.id = {$type->getId()}
            GROUP BY ar.id, app.number, ah.title
        ");

        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;
    }
}