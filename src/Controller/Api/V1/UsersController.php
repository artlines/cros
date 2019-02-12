<?php

namespace App\Controller\Api\V1;

use App\Entity\Conference;
use App\Entity\Participating\User;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * Class UsersController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1", name="api_v1__users_")
 */
class UsersController extends ApiController
{
    /**
     * @Route("/users/me", name="me", methods={"GET"})
     * @IsGranted("ROLE_CMS_USER")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param RoleHierarchyInterface $roleHierarchy
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function me(RoleHierarchyInterface $roleHierarchy)
    {
        /** @var User $user */
        $user = $this->getUser();

        /** @var Role[] $roles */
        $roles = array_map(function($role) {
            return new Role($role);
        }, $user->getRoles());


        $result = [
            'id'    => $user->getId(),
            'roles' => array_map(
                function($role) { return $role->getRole(); },
                $roleHierarchy->getReachableRoles($roles)
            ),
        ];

        return $this->success($result);
    }

    /**
     * @Route("/users/managers", name="managers", methods={"GET"})
     * @IsGranted("ROLE_CMS_USER")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    public function managers()
    {
        /** @var User[] $users */
        $users = $this->em->getRepository(User::class)->findBy(['roles' => '["ROLE_SALES_MANAGER"]']);

        $items = [];
        foreach ($users as $user) {
            $items[] = [
                'id'            => $user->getId(),
                'first_name'    => $user->getFirstName(),
                'last_name'     => $user->getLastName(),
            ];
        }

        return $this->success(['items' => $items, 'total_count' => count($items)]);
    }

    /**
     * @Route("/users", methods={"GET"}, name="users")
     * @IsGranted("ROLE_ADMINISTRATOR")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAll()
    {
        $year = date("Y");

        /** @var Conference $conference */
        if (!$conference = $this->em->getRepository(Conference::class)->findOneBy(['year' => $year])) {
            return $this->notFound("Conference with year $year not found.");
        }

        /** @var UserRepository $userRepo */
        $userRepo = $this->em->getRepository(User::class);

        /** @var User[] $users */
        list($users, $totalCount) = $userRepo->searchBy($this->requestData);

        return $this->success(['items' => $users, 'total_count' => $totalCount]);
    }

    /**
     * @Route("/users/roles", methods={"GET"})
     * @IsGranted("ROLE_ADMINISTRATOR")
     */
    public function getRoles()
    {
        $roles = [
            "ROLE_USER"                 => "Участник",
            "ROLE_SALES_MANAGER"        => "Менеджер",
            "ROLE_SETTLEMENT_MANAGER"   => "Менеджер по расселению",
            "ROLE_CONTENT_MANAGER"      => "Контент-менеджер",
            "ROLE_ADMINISTRATOR"        => "Администратор",
        ];

        $items = [];
        foreach ($roles as $key => $title) {
            $items[] = ['key' => $key, 'title' => $title];
        }

        return $this->success(['items' => $items]);
    }
}