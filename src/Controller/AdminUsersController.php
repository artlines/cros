<?php

namespace App\Controller;

use AppBundle\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminUsersController extends AbstractController
{
    /**
     * Список зарегистрированных пользователей
     *
     * @Route("/admin/registered", name="admin-registered")
     *
     * @return object
     */
    public function adminUsers(Request $request)
    {
        $offset = 0;
        $search_url = $request->getRequestUri();
        $url = parse_url($search_url,PHP_URL_QUERY);

        if($request->get('offset')) {
            $offset = $request->get('offset');
            parse_str($url, $url_output);
            unset($url_output['offset']);
            $url_output = http_build_query($url_output);
        }else{
            $url_output = $url;
        }
        $default_limit = 30;
        $users = array();

        $form_roles = $this->createFormBuilder()
            ->add('roles', ChoiceType::class, array(
                'mapped' => false,
                'choices' => array(
                    'USER' => 'ROLE_USER',
                    'SUPER ADMIN' => 'ROLE_SUPER_ADMIN',
                    'MANAGER' => 'ROLE_MANAGER',
                    'ORG' => 'ROLE_ORG',
                    'ADMIN' => 'ROLE_ADMIN',
                )
            ))
            ->getForm();
        $form = $this->createFormBuilder(null,array('attr'=>array('id' => 'search_form'),'method' => 'GET'))
            ->add('search', TextType::class, array('label' => 'запрос','attr'=>array('style' => 'width: 30%;float: left;margin-right: 10px;')))
            ->add('save', SubmitType::class, array('label' => 'Искать'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->get('search')->getData();
            /** @var User $users */
            $users = $this->getDoctrine()
                ->getRepository('App:User')
                ->easySearchUser($search,$offset,$default_limit);
        }else{
            $users = new \stdClass();
            $users->query = array();
            $users->count = 0;
        }
        return $this->render('admin/members/registered.html.twig', array(
            'users' => $users->query,
            'count' => $users->count,
            'default_limit'=>$default_limit,
            'form' => $form->createView(),
            'form_roles' => $form_roles->createView(),
            'url_output' => $url_output,
        ));
    }

    /**
     * Сброс пароля администратором
     *
     * @Route("/admin/reset-password-by-admin", name="reset-password-by-admin")
     *
     * @return object
     */
    public function resetPasswordAdmin(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Request $request */
        $id = $request->get('id');
        $new_password = $request->get('new_password');
        /** @var User $users */
        $user = $this->getDoctrine()
            ->getRepository('App:User')
            ->find($id);
        //$password = substr(md5($user->getLastName() . $user->getFirstName()), 0, 6);
        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $new_password);
        $user->setPassword($encoded);
        $em->persist($user);
        $em->flush();
        $status = array('ok');
        $response = new JsonResponse();
        $response->setData($status);
        return $response;
    }

    /**
     * Замена ролей пользователя
     *
     * @Route("/admin/reset-roles-by-admin", name="reset-roles-by-admin")
     *
     * @return object
     */
    public function resetRolesAdmin(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Request $request */
        $id = $request->get('id');
        $role = $request->get('role');
        /** @var User $users */
        $user = $this->getDoctrine()
            ->getRepository('App:User')
            ->find($id);
        $user->setRoles(array($role));
        $em->persist($user);
        $em->flush();
        $status = array('ok');
        $response = new JsonResponse();
        $response->setData($status);
        return $response;
    }

    /**
     * Блокировка пользователя
     *
     * @Route("/admin/lock-by-admin", name="lock-by-admin")
     *
     * @return object
     */
    public function lockAdmin(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Request $request */
        $id = $request->get('id');
        /** @var User $users */
        $user = $this->getDoctrine()
            ->getRepository('App:User')
            ->find($id);
        $user->setIsActive(0);
        $em->persist($user);
        $em->flush();
        $status = array('ok');
        $response = new JsonResponse();
        $response->setData($status);
        return $response;
    }

    /**
     * Разблокировка пользователя
     *
     * @Route("/admin/unlock-by-admin", name="unlock-by-admin")
     *
     * @return object
     */
    public function unlockAdmin(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Request $request */
        $id = $request->get('id');
        /** @var User $users */
        $user = $this->getDoctrine()
            ->getRepository('App:User')
            ->find($id);
        $user->setIsActive(1);
        $em->persist($user);
        $em->flush();
        $status = array('ok');
        $response = new JsonResponse();
        $response->setData($status);
        return $response;
    }

    /**
     * Создание нового пользователя
     *
     * @Route("/admin/creation-by-admin-user", name="creation-by-admin-user")
     *
     * @return object
     */
    public function creationUserAdmin(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $orgsts = $this->getDoctrine()->getRepository('App:Organization');
        $orgsts = $orgsts->findBy(array(), array('id' => 'ASC'));
        $boxOrgsts = array();
        foreach ($orgsts as $org){
            $boxOrgsts[$org->getName()] = $org->getId();
        }

        $form = $this->createFormBuilder()
            ->add('family', TextType::class, array('label' => 'Фамилия','required' => true))
            ->add('name', TextType::class, array('label' => 'Имя','required' => true))
            ->add('middle_name', TextType::class, array('label' => 'Отчество','required' => true))
            ->add('phone', TextType::class, array('label' => 'Телефон','required' => true))
            ->add('email', TextType::class, array('label' => 'E-mail','required' => true))
            ->add('password', PasswordType::class, array('label' => 'Пароль','required' => true))
            ->add('role', ChoiceType::class, array(
                'label' => 'Роль',
                'required' => true,
                'choices' => array(
                    'USER' => 'ROLE_USER',
                    'SUPER ADMIN' => 'ROLE_SUPER_ADMIN',
                    'MANAGER' => 'ROLE_MANAGER',
                    'ORG' => 'ROLE_ORG',
                    'ADMIN' => 'ROLE_ADMIN',
                )
            ))
            ->add('organization', ChoiceType::class, array(
                'label' => 'Организация',
                'choices'  => $boxOrgsts))
            ->add('save', SubmitType::class,array('label' => 'Сохранить') )
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $form = $form->getData();
            $orgsts = $this->getDoctrine()->getRepository('App:Organization')->find($form['organization']);
            /** @var User $user */
            $user = new User();
            $user->setOrganization($orgsts);
            $user->setFirstName($form['name']);
            $user->setLastName($form['family']);
            $user->setMiddleName($form['middle_name']);
            $user->setUsername($form['phone']); // It's actually a phone
            $user->setEmail($form['email']);
            $user->setIsActive(1);
            $password = substr(md5($user->getLastName().$user->getFirstName()), 0, 6);
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $password);
            $user->setPassword($encoded);
            $user->setRoles(array($form['role']));
            $em->persist($user);
            $em->flush();
            return $this->render('admin/members/adduser.html.twig', array(
                'status' => true
            ));
        }
        return $this->render('admin/members/adduser.html.twig', array(
            'form' => $form->createView(),
            'status' => false
        ));
    }
}
