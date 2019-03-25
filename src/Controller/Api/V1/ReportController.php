<?php

namespace App\Controller\Api\V1;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class RoomController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/report", name="api_v1__report__")
 * @IsGranted("ROLE_SETTLEMENT_MANAGER")
 */
class ReportController extends ApiController
{
    /**
     * @Route("/summary", name="summary")
     */
    public function summary()
    {
        $parameters = [
            'year' => date('Y'),
        ];

        $conn = $this->em->getConnection();

        $query = "
            WITH tmp_account AS (
              SELECT 
                     SUM(amount) as summa, 
                     COUNT(id) as cnt, 
                     array_agg(status_text) AS status, 
                     conference_organization_id
              FROM participating.invoice
              GROUP BY conference_organization_id
            )
            SELECT 
                   org.id, 
                   org.name as org_name, 
                   org.inn, 
                   org.kpp,
                   memb.last_name, 
                   memb.first_name, 
                   memb.middle_name, 
                   memb.post, 
                   memb.phone, 
                   memb.email,
                   rmtp.title, 
                   rmtp.cost,
                   mngr.last_name||' '||mngr.first_name as mngr_name,
                   aprt.number,
                   acnt.summa, 
                   acnt.status,
                   corg.notes
            FROM participating.organization org
              INNER JOIN participating.conference_organization  corg ON org.id = corg.organization_id
              INNER JOIN public.conference                      conf ON conf.id = corg.conference_id
              INNER JOIN participating.conference_member        cmem ON corg.id = cmem.conference_organization_id
              INNER JOIN participating.member                   memb ON memb.id = cmem.user_id
              LEFT  JOIN abode.room_type                        rmtp ON rmtp.id = cmem.room_type_id
              LEFT  JOIN participating.member                   mngr ON mngr.id = corg.invited_by
              LEFT  JOIN abode.place                           place ON cmem.id = place.conference_member_id
              LEFT  JOIN abode.room                             room ON room.id = place.room_id
              LEFT  JOIN abode.apartment                        aprt ON aprt.id = room.apartment_id
              LEFT  JOIN tmp_account                            acnt ON corg.id = acnt.conference_organization_id
            WHERE conf.year = :year
            ORDER BY org.name, org.inn, org.kpp, memb.last_name, memb.first_name, memb.middle_name
        ";

        $stmt = $conn->prepare($query);
        $stmt->execute($parameters);

        $result = $stmt->fetchAll();

        $report = $this->_calculateResponse('summary', 'Сводный отчет', $result);

        return $report;
    }

    private function _calculateResponse($alias, $filename, $data)
    {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$filename.'.csv');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Type: text/html; charset=windows-1251');

        $response = $this->render('report/'.$alias.'.html.twig', ['data' => $data]);

        $response->setCharset("WINDOWS-1251");
        $response->setContent(mb_convert_encoding($response->getContent(), "WINDOWS-1251", "UTF-8"));

        return $response;
    }
}