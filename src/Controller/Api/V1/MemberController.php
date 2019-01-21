<?php

namespace App\Controller\Api\V1;

use App\Entity\Participating\ConferenceMember;
use App\Entity\Participating\User;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class MemberController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/", name="api_v1__member__")
 * @IsGranted("ROLE_SETTLEMENT_MANAGER")
 */
class MemberController extends ApiController
{
    /**
     * @Route("member/{id}", requirements={"id":"\d+"}, methods={"PUT"}, name="update")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function update($id)
    {
        $first_name = $this->requestData['first_name'] ?? null;
        $last_name = $this->requestData['last_name'] ?? null;
        $middle_name = $this->requestData['middle_name'] ?? null;
        $post = $this->requestData['post'] ?? null;
        $phone = $this->requestData['phone'] ?? null;
        $email = $this->requestData['email'] ?? null;

        if (!$first_name || !$last_name || !$phone || !$email) {
            return $this->badRequest('Missing required param.');
        }

        /** @var ConferenceMember $member */
        if (!$member = $this->em->find(User::class, $id)) {
            return $this->notFound('Member not found.');
        }

        $member->setFirstName($first_name);
        $member->setLastName($last_name);
        $member->setMiddleName($middle_name);
        $member->setEmail($email);
        $member->setPhone($phone);
        $member->setPost($post);

        $this->em->persist($member);
        $this->em->flush();

        return $this->success();
    }
}