<?php

namespace App\Repository\Program;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;

class ProgramMemberRepository extends EntityRepository
{
    public function findByData($data)
    {
        $conn = $this->getEntityManager()->getConnection();
        $where = "TRUE";
        $parameters = ['year' => date('Y')];

        /** Check year param */
        if (isset($data['year'])) {
            $parameters['year'] = (int) $data['year'];
        }

        /** Check type param */
        if (isset($data['type'])) {
            $where .= " AND ppm.type = :type";
            $parameters['type'] = $data['type'];
        }

        $query = "
            SELECT
                   ppm.id,
                   pcm.id as conference_member_id,
                   pm.last_name,
                   pm.first_name,
                   pm.middle_name,
                   po.name as org_name,
                   ppm.photo_original,
                   ppm.publish,
                   ppm.description,
                   ppm.type,
                   ppm.ordering
            FROM program.program_member ppm
              LEFT JOIN participating.conference_member pcm ON ppm.conference_member_id = pcm.id
              INNER JOIN public.conference pc ON pcm.conference_id = pc.id AND pc.year = :year
              LEFT JOIN participating.member pm ON pcm.user_id = pm.id
              LEFT JOIN participating.organization po ON pm.organization_id = po.id
            WHERE $where
        ";

        try {
            $stmt = $conn->prepare($query);
            $stmt->execute($parameters);
            return $stmt->fetchAll();
        } catch (DBALException $e) {
            return [];
        }
    }
}