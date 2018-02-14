<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminUsersController extends Controller
{
    /**
     * Список зарегистрированных пользователей
     *
     * @Route("/admin/registered", name="admin-registered")
     *
     * @return object
     */
    public function adminUsersAction()
    {
        /** @var User $users */
        $users = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAll();

        return $this->render('admin/members/registered.html.twig', array(
            'users' => $users,
        ));
    }
}
