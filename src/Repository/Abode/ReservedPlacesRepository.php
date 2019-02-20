<?php

namespace App\Repository\Abode;

use Doctrine\ORM\EntityRepository;

class ReservedPlacesRepository extends EntityRepository
{
    /**
     * Return array with elements like ['room_type_id' => 'count of reserved places']
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    public function getReservedPlacesInfo()
    {

    }
}