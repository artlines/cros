<?php

namespace App\Controller\Api\V1\Program;

use App\Controller\Api\V1\ApiController;
use App\Entity\Program\ProgramMember;
use App\Repository\Program\ProgramMemberRepository;

/**
 * Class ApartmentController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/", name="api_v1__program_member__")
 * @IsGranted("ROLE_CONTENT_MANAGER")
 */
class ProgramMemberController extends ApiController
{
    /**
     * @Route("program_member", name="program_member__get", methods={"GET"})
     */
    public function getAll()
    {
        /**
         * Must returns from repository
         *
         * fio
         * organization
         * photo
         * publish
         * type
         */
        /** @var ProgramMemberRepository $programMemberRepo */
        $programMemberRepo = $this->em->getRepository(ProgramMember::class);

        $items = $programMemberRepo->findByData($this->requestData);

        return $this->success(['items' => $items]);
    }
}