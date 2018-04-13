<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Logs;
use AppBundle\Entity\Organization;
use AppBundle\Entity\User;
use AppBundle\Repository\LogsRepository;
use AppBundle\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SmsController extends Controller
{
    /**
     * Реализация отправки Смс через шлюз Zanzara
     */
    protected $url = 'https://www.contentum-it.ru/xml/';
    protected $login = 'MSU_SHOPNAGRU';
    protected $password = 'dihf9831';
    protected $messages = array();
    protected $xml = "";

    /**
     * Get url
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get login
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Get password
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get messages
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Create Sms list
     *
     * @return SmsController
     */
    public function createSmsList()
    {
        $messages = $this->getMessages();
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>';
        $xml .= '<xml_request name="sms_send">';
        $xml .= '<xml_user lgn="' . $this->getLogin() . '" pwd="' . $this->getPassword() . '" />';
        foreach ($messages as $message) {
            $xml .= '<sms sms_id="' . $message['id'] . '" number="' . $message['number'] . '" source_number="' . $message['source_number'] . '">' . $message['text'] . '</sms>';
        }
        $xml .= '</xml_request>';

        $this->xml = $xml;

        return $this;
    }

    /**
     * Get XML
     *
     * @return string
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * Add message
     * @param $id integer Уникальный идентификатор сообщения
     * @param $number integer Номер получателя
     * @param $text string Текст сообщения
     * @param $source_number string Отправитель (не более 11 символов)
     *
     * @return SmsController
     */
    public function addMessage($id, $number, $text, $source_number = 'KPOC-2.0-17')
    {
        $this->messages[$id] = array(
            'id' => $id,
            'number' => $number,
            'source_number' => $source_number,
            'text' => $text,
        );
        return $this;
    }

    /**
     * Get message
     * @param $id integer Идентификатор сообщения
     * @return array
     */
    public function getMessage($id)
    {
        return $this->messages[$id];
    }

    /**
     * Send
     */
    public function send()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getUrl());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getXml());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * Generate and send password
     *
     * @Route("/sms-password", name="sms-password")
     *
     * @param Request $request
     * @return object
     */
    public function generatePasswordAction(Request $request){
        $phone = $request->get('phone');
        $date = date('Y-m-d 00:00:00');

        /** @var LogsRepository $logsRepository */
        $logsRepository = $this->getDoctrine()->getRepository('AppBundle:Logs');
        /** @var Logs $logs */
        /*$logs = $logsRepository->getConnectionOlder($date);
        foreach ($logs as $log){
            var_dump($log); die();
        }*/

        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
        /** @var User $user */
        $user = $userRepository->findOneBy(array('username' => $phone));
        if($user) {
            $em = $this->getDoctrine()->getManager();
            $password = substr(md5($user->getLastName().$user->getFirstName()), 0, 6);
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $password);

            $user->setPassword($encoded);
            $em->persist($user);
            $em->flush();

            $log = new Logs();
            $log->setEntity('connection');
            $log->setEvent('Отправлен пароль '.$password.' для '.$phone);
            $log->setReaded(0);
            $log->setElementId($user->getId());
            $log->setDate(new \DateTime('now'));

            $em->persist($log);
            $em->flush();

            $uniq = $log->getId();

            $this->addMessage('cros_' . $uniq, $user->getUsername(), 'Ваш пароль для связи с участниками КРОС: '.$password);

            $result = $this->createSmsList()->send();

            return $this->render('frontend/members/sms.html.twig', array('response' => '<span class="text-success">Пароль отправлен</span>'));
        }
        else{
            return $this->render('frontend/members/sms.html.twig', array('response' => '<span class="text-danger">Данный номер не найден в базе</span>'));
        }
    }

    /**
     * @Route("/smscheck", name="smscheck")
     */
    public function smsCheckAction(){
        $connection = array(
            'org_name' => '',
            'org_id' => 1,
            'text' => 'Привет',
            'from' => 'Иван из НАГ'
        );
        $organizationRepository = $this->getDoctrine()->getRepository('AppBundle:Organization');
        $org = $organizationRepository->find($connection["org_id"]);
        if ($org) {
            $users = $org->getUsers();
            /** @var User $user */
            foreach ($users as $user) {
                if (filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                    if ($user->getEmail() != "needsetemail@gmail.com") {
                        $uem = $user->getEmail();
                        $message = \Swift_Message::newInstance()
                            ->setSubject('КРОС-2.0-17: Сообщение от '.$connection['from'])
                            ->setFrom('cros@nag.ru')
                            ->setTo(array('xvanok@nag.ru'))
                            //->setBcc(array('xvanok@nag.ru'))
                            ->setBody(
                                $this->renderView(
                                    'Emails/main.html.twig',
                                    array(
                                        'text' => $connection['from'].' пишет: '.$connection['text'],
                                    )
                                ),
                                'text/html'
                            );
                        $res = $this->get('mailer')->send($message);
                        break;
                    }
                }
            }
        }
        return new Response($uem.' '.$res);
    }

    /**
     * Send sms for members
     *
     * @Route("/sms", name="sms")
     *
     * @param Request $request
     * @return object
     */
    public function connectionSmsAction(Request $request)
    {
        $session = $request->getSession();
        $send_auth = $session->get('sendauth');
        if($send_auth) {
            $em = $this->getDoctrine()->getManager();

            $connection_json = $request->get('connection');
            $connection = json_decode($connection_json, 1);

            $organizationRepository = $this->getDoctrine()->getRepository('AppBundle:Organization');

            //if ($connection["org_id"] == 1 || $connection["org_id"] == 378) {

                $org = $organizationRepository->find($connection["org_id"]);

                /** @var Organization $org */
                if ($org) {
                    $users = $org->getUsers();
                    /** @var User $user */
                    foreach ($users as $user) {
                        $log = new Logs();
                        $log->setEntity('connection');
                        $log->setEvent($connection_json);
                        $log->setReaded(0);
                        $log->setElementId($user->getId());
                        $log->setDate(new \DateTime('now'));

                        $em->persist($log);
                        $em->flush();

                        $uniq = $log->getId();

                        $this->addMessage('cros_' . $uniq, $user->getUsername(), $connection['from'] . ' пишет: ' . $connection['text']);

                        if (filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                            if ($user->getEmail() != "needsetemail@gmail.com") {
                                $message = \Swift_Message::newInstance()
                                    ->setSubject('КРОС-2.0-17: Сообщение от '.$connection['from'])
                                    ->setFrom('cros@nag.ru')
                                    ->setTo($user->getEmail())
                                    //->setBcc(array('xvanok@nag.ru', 'cros@nag.ru', 'esuzev@nag.ru'))
                                    ->setBody(
                                        $this->renderView(
                                            'Emails/main.html.twig',
                                            array(
                                                'text' => $connection['from'].' пишет: '.$connection['text'],
                                            )
                                        ),
                                        'text/html'
                                    );
                                $this->get('mailer')->send($message);
                            }
                        }


                    }
                    $result = $this->createSmsList()->send();

                    return $this->render('frontend/members/sms.html.twig', array('response' => '<span class="text-success">Сообщение отправлено</span>'));
                }
            //} else {
            //    return $this->render('frontend/members/sms.html.twig', array('response' => '<span class="text-success">Отправка сообщений выбранной организации закрыта на время теста</span>'));

            //}
        }
        else{
            return $this->render('frontend/members/sms.html.twig', array('response' => '<span class="text-success">Отправка не удалась, обновите страницу и авторизуйтесь заново</span>'));
        }
    }
}
