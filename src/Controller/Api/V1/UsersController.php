<?php

namespace App\Controller\Api\V1;

use App\Entity\Participating\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * Class UsersController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/users", name="api_v1__users_")
 */
class UsersController extends ApiController
{
    /**
     * @Route("/me", name="me", methods={"GET"})
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
}