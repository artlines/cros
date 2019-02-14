<?php

namespace App\Controller\Api\V1;

use App\Entity\Participating\Organization;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class OrganizationController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1", name="api_v1__organizations_")
 */
class OrganizationController extends ApiController
{
    /**
     * @Route("/organization_directory", methods={"GET"})
     * @IsGranted("ROLE_ADMINISTRATOR")
     */
    public function getDirectory()
    {
        /** @var Organization[] $organizations */
        $organizations = $this->em->getRepository(Organization::class)->findBy([], ['name' => 'ASC']);

        $items = [];
        foreach ($organizations as $org) {
            $items[] = [
                'id'    => $org->getId(),
                'name'  => $org->getName()
            ];
        }

        return $this->success(['items' => $items]);
    }
}