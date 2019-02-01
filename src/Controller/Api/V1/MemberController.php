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
}