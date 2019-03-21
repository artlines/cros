<?php

namespace App\Controller\Api\V1;

use App\Entity\Conference;
use App\Entity\Participating\Organization;
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
        $users = $this->em
            ->getRepository(User::class)
            ->findBy([
                'roles' => '["ROLE_SALES_MANAGER"]'
            ],[
                'lastName'=>'ASC',
                'firstName'=>'ASC'
            ]);

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

    /**
     * @Route("/users/new", methods={"POST"})
     * @IsGranted("ROLE_ADMINISTRATOR")
     */
    public function new()
    {
        $email = $this->requestData['email'] ?? null;
        $first_name = $this->requestData['first_name'] ?? null;
        $last_name = $this->requestData['last_name'] ?? null;
        $organization_id = $this->requestData['organization_id'] ?? null;
        $phone = $this->requestData['phone'] ?? null;
        $sex = $this->requestData['sex'] ?? null;
        $role = $this->requestData['role'] ?? null;
        $middle_name = $this->requestData['middle_name'] ?? null;
        $post = $this->requestData['post'] ?? null;
        $is_active = $this->requestData['is_active'] ?? null;
        $representative = $this->requestData['representative'] ?? null;

        if (!$email || !$first_name || !$last_name || !$organization_id || !$phone || !$role || !$sex) {
            return $this->badRequest('Не указаны обязательные параметры');
        }

        if ($this->em->getRepository(User::class)->findOneBy(['email' => $email])) {
            return $this->badRequest('Данный email уже используется.');
        }

        /** @var Organization $organization */
        if (!$organization = $this->em->find(Organization::class, $organization_id)) {
            return $this->notFound('Organization not found.');
        }

        $user = new User();
        $user->setFirstName($first_name);
        $user->setLastName($last_name);
        $user->setEmail($email);
        $user->setOrganization($organization);
        $user->setPhone($phone);
        $user->setSex($sex);
        $user->setRoles([$role]);
        $user->setMiddleName($middle_name);
        $user->setPost($post);
        $user->setPassword('not');

        if (is_bool($is_active)) {
            $user->setIsActive($is_active);
        }

        if (is_bool($representative)) {
            $user->setRepresentative($representative);
        }

        $this->em->persist($user);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("/users/{id}", requirements={"id":"\d+"}, methods={"PUT"})
     * @IsGranted("ROLE_ADMINISTRATOR")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function edit($id)
    {
        $email = $this->requestData['email'] ?? null;
        $first_name = $this->requestData['first_name'] ?? null;
        $last_name = $this->requestData['last_name'] ?? null;
        $organization_id = $this->requestData['organization_id'] ?? null;
        $phone = $this->requestData['phone'] ?? null;
        $sex = $this->requestData['sex'] ?? null;
        $role = $this->requestData['role'] ?? null;
        $middle_name = $this->requestData['middle_name'] ?? null;
        $post = $this->requestData['post'] ?? null;
        $is_active = $this->requestData['is_active'] ?? null;
        $representative = $this->requestData['representative'] ?? null;

        if (!$email || !$first_name || !$last_name || !$organization_id || !$phone || !$role || !$sex) {
            return $this->badRequest('Не указаны обязательные параметры');
        }

        /** @var Organization $organization */
        if (!$organization = $this->em->find(Organization::class, $organization_id)) {
            return $this->notFound('Organization not found.');
        }

        /** @var User $user */
        if (!$user = $this->em->find(User::class, $id)) {
            return $this->notFound('User not found.');
        }

        if ($user !== $this->em->getRepository(User::class)->findOneBy(['email' => $email])) {
            return $this->badRequest('Данный email уже используется.');
        }

        $user->setFirstName($first_name);
        $user->setLastName($last_name);
        $user->setEmail($email);
        $user->setOrganization($organization);
        $user->setPhone($phone);
        $user->setRoles([$role]);
        $user->setMiddleName($middle_name);
        $user->setPost($post);
        $user->setSex($sex);

        if (is_bool($is_active)) {
            $user->setIsActive($is_active);
        }

        if (is_bool($representative)) {
            $user->setRepresentative($representative);
        }

        $this->em->persist($user);
        $this->em->flush();

        return $this->success();
    }
}