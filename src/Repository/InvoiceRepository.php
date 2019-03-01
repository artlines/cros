<?php

namespace App\Repository;

use App\Entity\Participating\Invoice;
use Doctrine\ORM\EntityRepository;

class InvoiceRepository extends EntityRepository
{
    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @return Invoice[]
     */
    public function getWithOrderGuidToSync()
    {
        $qb = $this->createQueryBuilder('i');

        $query = $qb
            ->select('i')
            ->where($qb->expr()->isNotNull('i.orderGuid'));

        return $query->getQuery()->getResult();
    }
}