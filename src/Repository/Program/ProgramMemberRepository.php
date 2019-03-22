<?php

namespace App\Repository\Program;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;

class ProgramMemberRepository extends EntityRepository
{
    public function findByData($data)
    {
        $conn = $this->getEntityManager()->getConnection();

        $query = "
            
        ";

        try {
            $stmt = $conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (DBALException $e) {
            return [];
        }
    }
}