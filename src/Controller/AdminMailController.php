<?php

namespace App\Controller;

use AppBundle\Entity\Conference;
use AppBundle\Entity\Info;
use AppBundle\Entity\InfoToConf;
use AppBundle\Entity\Logs;
use AppBundle\Entity\Mail;
use AppBundle\Entity\Organization;
use AppBundle\Entity\OrganizationStatus;
use AppBundle\Entity\OrgToConf;
use AppBundle\Entity\UserToConf;
use AppBundle\Repository\MailRepository;
use Symfony\Component\Form\Form;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;

class AdminMailController extends AbstractController
{

    /**
     * Рассылка
     * @Route("/admin/mailing", name="admin-mailing")
     * @param Request $request
     * @return object
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $mailsRepository = $this->getDoctrine()
            ->getRepository('App:Mail');
        $mails = $mailsRepository->findBy(array(), array('id' => 'DESC'));

        $utcs = $this->getDoctrine()
            ->getRepository('App:UserToConf')
            ->findAll();
        ini_set('memory_limit', '1024M');

        if (in_array($request->get('send'), array('test', 'prod'))) {
            set_time_limit(count($utcs) * 10);
            /** @var UserToConf $utc */
            foreach ($utcs as $utc) {
                $user = $utc->getUser();
                if ($user->getEmail() == "navu@nag.ru" || $request->get('send') == 'prod') {
                    if (filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                        if ($user->getEmail() != "needsetemail@gmail.com") {
                            /** @var Mail $mail */
                            foreach ($mails as $mail) {
                                $chsend = $this->getDoctrine()
                                    ->getRepository('App:Logs')
                                    ->findOneBy(array('entity' => 'mail', 'event' => $user->getEmail(), 'elementId' => $mail->getId()));
                                if (!$chsend) {
                                    if ($mail->getSended() === 0) {
                                        $text = $mail->getText();
                                        if ($user->getPost() != null && $user->getPost() != '-') {
                                            $text = str_replace('{{ user.post }}', $user->getPost(), $text);
                                        } else {
                                            $text = str_replace('{{ user.post }}', 'Сотрудник', $text);
                                        }
                                        $text = str_replace('{{ user.org }}', $user->getOrganization()->getName(), $text);
                                        $text = str_replace('{{ user.name }}', $user->getFirstName() . ' ' . $user->getLastName(), $text);
                                        $message = \Swift_Message::newInstance()
                                            ->setSubject($mail->getSubject())
                                            ->setFrom('cros@nag.ru')
                                            ->setTo($user->getEmail())
                                            ->setBcc(array('xvanok@nag.ru', 'cros@nag.ru', 'esuzev@nag.ru'))
                                            ->setBody(
                                                $this->renderView(
                                                    'Emails/main.html.twig',
                                                    array(
                                                        'text' => $text,
                                                    )
                                                ),
                                                'text/html'
                                            );
                                        $this->get('mailer')->send($message);

                                        if($request->get('send') == 'prod') {
                                            /** @var Logs $logs */
                                            $logs = new Logs();
                                            $logs->setEntity('mail');
                                            $logs->setElementId($mail->getId());
                                            $logs->setReaded(0);
                                            $logs->setEvent($user->getEmail());

                                            $logs->setDate(new \DateTime('now'));
                                            $em->persist($logs);
                                            $em->flush();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if($request->get('send') == 'prod'){
            foreach ($mails as $mail){
                $mail->setSended(1);
                $em->persist($mail);
                $em->flush();
            }
        }

        return $this->render('admin/mailing/all.html.twig', array(

            'mails' => $mails,

        ));
    }

    /**
     * @Route("/admin/mailing/{id}", name="admin-mailing-view")
     * @param integer $id
     * @param Request $request
     * @return object
     */
    public function view($id, Request $request){
        /** @var MailRepository $mailRepository */
        $mailRepository = $this->getDoctrine()->getRepository('App:Mail');
        /** @var Mail $mail */
        $mail = $mailRepository->find($id);

        if($mail->getSended() == 1) {
            return $this->render('admin/mailing/view.html.twig', array(

                'mail' => $mail,
            ));
        }
        else{

        }
    }
}
