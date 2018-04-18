<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Hall;
use AppBundle\Entity\Lecture;
use AppBundle\Entity\Logs;
use AppBundle\Entity\Organization;
use AppBundle\Entity\User;
use AppBundle\Manager\TgChatManager;
use AppBundle\Service\Sms;
use AppBundle\Service\Telegram;
use AppBundle\Repository\LectureRepository;
use AppBundle\Repository\OrganizationRepository;
use AppBundle\Repository\TgChatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Monolog\Logger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\TgChat;
use Symfony\Component\Routing\Exception\InvalidParameterException;

class WebhookController extends Controller
{
    const LECTURES_ON_PAGE = 5;
    const MY_LECTURES_ON_PAGE = 5;
    const CONTACTS_ON_PAGE = 8;

    /** @var Telegram */
    private $bot;

    /** @var TgChat */
    private $tgChat;

    /** @var array */
    private $update;

    /** @var TgChatManager */
    private $tsm;

    /** @var Sms */
    private $sms;

    /** @var string */
    private $access_token = 'c2hhbWJhbGEyMykxMiUh';

    // setWebhook / stage.cros.nag.ru
    // https://api.telegram.org/bot527782633:AAFPLooKU0KwINR_CwRj7R-1Z_nHv9b5t0o/setWebhook?url=https://proxy-web.nag.how:88/webhook/update/c2hhbWJhbGEyMykxMiUh

    /**
     * Прием обновлений
     *
     * @Route("/webhook/update/{token}", name="webhook-update")
     * @param $token
     * @return Response
     */
    public function update($token)
    {
        if ($this->access_token === $token) {
            // Для очистки повисших запросов
            //return new Response('ok', 200);
            try {
                $this->init_bot();

                $this->update = json_decode(file_get_contents('php://input'), true);
                $this->_debug($this->update);

                $this->tgChat = $this->_findTgChat();
                $this->tsm = $this->get('tg.chat.manager');
                $this->sms = $this->get('sms.service');

                $this->process();
            } catch (OptimisticLockException $e) {
                $this->_error($e);
            } catch (\Exception $e) {
                $this->_error($e);
            }

            return new Response('ok', 200);
        } else {
            return new Response('Permission denied.', 403);
        }
    }

    /**
     * Разбор комманд
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws EntityNotFoundException
     */
    private function process()
    {
        if (isset($this->update['message'])) {
            if (isset($this->update['message']['reply_to_message'])) {
                $tgState = $this->tgChat->getState();
                if (isset($tgState['reply_type'])) {
                    $_text = trim($this->update['message']['text']);
                    switch ($tgState['reply_type']) {
                        case 'contact_with':
                            $this->_debug(['$tgState' => $tgState]);
                            if (!isset($tgState['_name'])) {
                                $this->_contactWith($tgState['_org_id'], $_text);
                            } elseif (!isset($tgState['_company'])) {
                                $this->_contactWith($tgState['_org_id'], $tgState['_name'], $_text);
                            } elseif (!isset($tgState['_phone'])) {
                                $this->_contactWith($tgState['_org_id'], $tgState['_name'], $tgState['_company'], $_text);
                            } else {
                                throw new InvalidParameterException("All parameters must be defined.");
                            }
                            break;
                    }
                } else {
                    // Ничего. Молчим, если по стейту мы не ожидаем никакого ответа на сообщение
                }
            } else {
                switch (trim($this->update['message']['text'])) {
                    case '/start':
                        $this->_start();
                        break;
                    case '/stop':
                        $this->_stop();
                        break;
                    case '/menu':
                        $this->_menu();
                        break;
                    case 'МЕНЮ':
                        $this->_menu();
                        break;
                    case 'Посмотреть расписание':
                        $this->_showDates();
                        break;
                    case 'Мое расписание':
                        $this->_mySubscribes(1);
                        break;
                    case 'Уведомлять о начале докладов':
                        $this->_notifyMe();
                        break;
                    case 'Написать участнику':
                        $this->_contactList(1);
                        break;
                    default:
                        break;
                }
            }
        } else {
            $args = explode(":", trim($this->update['callback_query']['data']));

            // log
            $this->_debug($args);

            switch ($args[0]) {
                case 'show_dates':
                    $this->_showDates();
                    break;
                case 'show_halls':
                    $this->_showHalls($args[1]);
                    break;
                case 'show_lectures':
                    $this->_showLectures($args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);
                    break;
                case 'my_subscribes':
                    $this->_mySubscribes($args[1], $args[2], $args[3]);
                    break;
                case 'notify':
                    $this->_notifyMe($args[1]);
                    break;
                case 'contact_list':
                    $this->_contactList($args[1]);
                    break;
                case 'contact_with':
                    $this->tsm->resetState($this->tgChat);
                    $this->_contactWith($args[1], $args[2], $args[3], $args[4]);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * Command: /start
     */
    private function _start()
    {
        $chat_id = $this->update['message']['chat']['id'];
        $username = $this->update['message']['from']['username'];

        $text = "Привет, $username! Я бот конференции КРОС 2018.\n"
            ."Помогу следить за расписанием, быть в курсе событий.\n"
            ."Если понадоблюсь - жми МЕНЮ. Продуктивного тебе времяпровождения!";

        $options = array(
            array(),
            array($this->bot->buildKeyboardButton("МЕНЮ")),
            array()
        );
        $keyBoard = $this->bot->buildKeyBoard($options, false, true);

        $content = array('chat_id' => $chat_id, 'text' => $text, 'reply_markup' => $keyBoard);
        $this->bot->sendMessage($content);

        $em = $this->getDoctrine()->getManager();

        /** @var TgChatRepository $repo */
        $repo = $this->getDoctrine()->getRepository('AppBundle:TgChat');

        /** @var TgChat $chat */
        $chat = $repo->findOneByChatId($chat_id);

        if ($chat) {
            if (false === $chat->getIsActive()) {
                $chat->setIsActive(true);
                $em->persist($chat);
                $em->flush();
            }
        } else {
            $chat = new TgChat();
            $chat->setChatId($chat_id);
            $em->persist($chat);
            $em->flush();
        };
    }

    /**
     * Command: /stop
     */
    private function _stop()
    {
        $chat_id = $this->update['message']['chat']['id'];

        $options = array();
        $keyBoard = $this->bot->buildKeyBoard($options, false, true);

        $content = array('chat_id' => $chat_id, 'text' => 'stop', 'reply_markup' => $keyBoard);
        $this->bot->sendMessage($content);

        $em = $this->getDoctrine()->getManager();

        /** @var TgChatRepository $repo */
        $repo = $this->getDoctrine()->getRepository('AppBundle:TgChat');

        /** @var TgChat $chat */
        $chat = $repo->findOneByChatId($chat_id);

        if ($chat)
        {
            if (true === $chat->getIsActive())
            {
                $chat->setIsActive(false);
                $em->persist($chat);
                $em->flush();
            }
        }
        else
        {

        };
    }

    /**
     * Command: "МЕНЮ"
     */
    private function _menu()
    {
        $options = array(
            array($this->bot->buildKeyboardButton("Посмотреть расписание")),
            array($this->bot->buildKeyboardButton("Мое расписание")),
            array($this->bot->buildKeyboardButton("Уведомлять о начале докладов")),
            array($this->bot->buildKeyboardButton("Написать участнику"))
        );
        $keyBoard = $this->bot->buildKeyBoard($options, true, true);

        $content = array(
            'chat_id' => $this->update['message']['chat']['id'],
            'text' => 'Чем могу помочь?',
            'reply_markup' => $keyBoard
        );

        $this->bot->sendMessage($content);
    }

    /**
     * Command: "Мое расписание"
     */
    private function _mySubscribes($page = 1, $lecture_action = null, $lecture_id = null)
    {
        $buttons = array();

        if (isset($lecture_action, $lecture_id) && $lecture_action == 'info') {
            $lecture = $this->_findLecture($lecture_id);

            $text = $this->renderView('telegram_bot/lecture_info.html.twig', ['lecture' => $lecture]);

            $buttons[] = array(
                $this->bot->buildInlineKeyBoardButton(
                    "Отписаться",
                    false,
                    "my_subscribes:1:unsubscribe:$lecture_id"
                )
            );
            $buttons[] = array(
                $this->bot->buildInlineKeyBoardButton(
                    "НАЗАД",
                    false,
                    "my_subscribes:$page"
                )
            );

        } else {

            if (isset($lecture_action, $lecture_id) && $lecture_action == 'unsubscribe') {
                $lecture = $this->_findLecture($lecture_id);
                $this->tsm->unsubscribeLecture($this->tgChat, $lecture);
            }

            $subscribes = $this->tgChat->getLectures();
            if (!$subscribes->isEmpty()) {
                $sub_show = new ArrayCollection($subscribes->slice(($page-1) * self::MY_LECTURES_ON_PAGE, self::MY_LECTURES_ON_PAGE));
                /** @var Lecture $subscribe */
                foreach ($sub_show as $subscribe) {
                    $buttons[] = array($this->bot->buildInlineKeyBoardButton(
                        $subscribe->getStartTime()->format("H:i") . " " . $subscribe->getDate()->format("d.m.Y") . " | " . $subscribe->getTitle(),
                        false,
                        "my_subscribes:$page:info:{$subscribe->getId()}"
                    ));
                }
            }

            /**
             * Если лекций больше, чем можно выводить на страницу, то
             * - пишем номер страницы
             * - добавляем кнопки пагинации
             */
            $paginator = '';
            if ($subscribes->count() > self::MY_LECTURES_ON_PAGE) {
                $totalPages = round($subscribes->count() / self::MY_LECTURES_ON_PAGE, 0,PHP_ROUND_HALF_UP);
                $paginator = $this->renderView('telegram_bot/_paginator_text.html.twig', ['current_page' => $page, 'total_pages' => $totalPages]);

                if ($totalPages > 1) {
                    if ($page == 1) {
                        $buttons[] = array($this->bot->buildInlineKeyBoardButton(">>>", false, 'my_subscribes:' . ($page + 1)));
                    } elseif ($page == $totalPages) {
                        $buttons[] = array($this->bot->buildInlineKeyBoardButton("<<<", false, 'my_subscribes:' . ($page - 1)));
                    } else {
                        $buttons[] = array(
                            $this->bot->buildInlineKeyBoardButton("<<<", false, 'my_subscribes:' . ($page - 1)),
                            $this->bot->buildInlineKeyBoardButton(">>>", false, 'my_subscribes:' . ($page + 1))
                        );
                    }
                }
            }

            $text = $this->renderView('telegram_bot/my_subscribes.html.twig', ['count' => $subscribes->count()]).$paginator;
        }

        if (isset($this->update['message'])) {
            $content = array(
                'chat_id' => $this->update['message']['chat']['id'],
                'text' => $text,
                'parse_mode' => 'HTML',
                'reply_markup' => $this->bot->buildInlineKeyBoard($buttons)
            );
            $this->bot->sendMessage($content);
        } else {
            $content = array(
                'chat_id' => $this->update['callback_query']['message']['chat']['id'],
                'message_id' => $this->update['callback_query']['message']['message_id'],
                'text' => $text,
                'parse_mode' => 'HTML',
                'reply_markup' => $this->bot->buildInlineKeyBoard($buttons)
            );
            $this->bot->editMessageText($content);
        }
    }

    /**
     * Command: "Написать учаcтнику"
     *
     * @param int $page
     */
    public function _contactList($page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var OrganizationRepository $organizations */
        $orgRepo = $em->getRepository('AppBundle:Organization');

        $org_count = $orgRepo->findByIdsOrganizationApproved(['count' => true]);
        $orgs = $orgRepo->findByIdsOrganizationApproved([
            'limit' => self::CONTACTS_ON_PAGE,
            'offset' => ($page - 1) * self::CONTACTS_ON_PAGE
        ]);

        $org_list = array();
        $buttons = array();
        /** @var Organization $org */
        foreach ($orgs as $org) {
            $buttons[] = array(
                $this->bot->buildInlineKeyBoardButton(
                    $org['name'],
                    false,
                    "contact_with:{$org['id']}"
                )
            );
            $org_list[] = $org['name'];
        }

        $text = $this->renderView('telegram_bot/contacts_list.html.twig');

        /**
         * Пагинаццция
         */
        if ($org_count > self::CONTACTS_ON_PAGE) {
            $totalPages = round($org_count / self::CONTACTS_ON_PAGE, 0,PHP_ROUND_HALF_UP);
            $text .= $this->renderView('telegram_bot/_paginator_text.html.twig', ['current_page' => $page, 'total_pages' => $totalPages]);

            if ($totalPages > 1) {
                if ($page == 1) {
                    $buttons[] = array($this->bot->buildInlineKeyBoardButton(">>>", false, 'contact_list:'.($page+1)));
                } elseif ($page == $totalPages) {
                    $buttons[] = array($this->bot->buildInlineKeyBoardButton("<<<", false, 'contact_list:'.($page-1)));
                } else {
                    $buttons[] = array(
                        $this->bot->buildInlineKeyBoardButton("<<<", false, 'contact_list:'.($page-1)),
                        $this->bot->buildInlineKeyBoardButton(">>>", false, 'contact_list:'.($page+1))
                    );
                }
            }
        }

        if (isset($this->update['message'])) {
            $content = array(
                'chat_id' => $this->update['message']['chat']['id'],
                'text' => ($text == '') ? 'Нет данных' : $text,
                'parse_mode' => 'HTML',
                'reply_markup' => $this->bot->buildInlineKeyBoard($buttons)
            );
            $this->bot->sendMessage($content);
        } else {
            $content = array(
                'chat_id' => $this->update['callback_query']['message']['chat']['id'],
                'message_id' => $this->update['callback_query']['message']['message_id'],
                'text' => ($text == '') ? 'Нет данных' : $text,
                'parse_mode' => 'HTML',
                'reply_markup' => $this->bot->buildInlineKeyBoard($buttons)
            );
            $this->bot->editMessageText($content);
        }
    }

    /**
     * Command: "Уведомлять о начале докладов"
     */
    private function _notifyMe($flag = null)
    {
        /**
         * Показываем меню/статус уведомлений
         * либо
         * Включаем/отключаем уведомления
         */
        if (isset($this->update['message'])) {
            try {
                if ($this->tgChat->isAllowNotify()) {
                    $_status = 'Вы подписаны на уведомления';
                } else {
                    $_status = 'Вы отписаны от уведомлений';
                }
            } catch (EntityNotFoundException $e) {
                return false;
            }
            $this->_debug(['status' => $this->tgChat->isAllowNotify()]);

            $option = array(array(
                $this->bot->buildInlineKeyBoardButton(
                    "Нет",
                    false,
                    "notify:0"
                ),
                $this->bot->buildInlineKeyBoardButton(
                    "Да",
                    false,
                    "notify:1"
                )
            ));
            $content = array(
                'chat_id' => $this->update['message']['chat']['id'],
                'text' => $this->renderView('telegram_bot/notify_setting.html.twig'),
                'reply_markup' => $this->bot->buildInlineKeyBoard($option),
                'parse_mode' => 'HTML'
            );
            $this->bot->sendMessage($content);
        } else {
            $em = $this->getDoctrine()->getManager();
            if ($flag) {
                $this->tgChat->allowNotify();
                $_status = 'Вы подписаны на уведомления';
            } else {
                $this->tgChat->denyNotify();
                $_status = 'Вы отписаны от уведомлений';
            }

            $em->persist($this->tgChat);
            $em->flush();

            $content = array(
                'chat_id' => $this->update['callback_query']['message']['chat']['id'],
                'message_id' => $this->update['callback_query']['message']['message_id'],
                'text' => $this->renderView('telegram_bot/notify_setting.html.twig', ['status' => $_status]),
                'parse_mode' => 'HTML'
            );
            $this->bot->editMessageText($content);
        }
    }

    /**
     * Command: "Посмотреть расписание"
     */
    private function _showDates()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT DISTINCT l.date FROM AppBundle\Entity\Lecture l ORDER BY l.date ASC');
        $dates = $query->getResult();

        $option = array();
        /** @var Hall $hall */
        foreach ($dates as $date) {
            $date_title = $date['date']->format("d.m.Y");
            $date_str = $date['date']->format("Y-m-d");
            $option[] = array($this->bot->buildInlineKeyBoardButton($date_title, false, 'show_halls:' . $date_str));
        }
        $inlineKeyBoard = $this->bot->buildInlineKeyBoard($option);

        /**
         * Проверяем событие
         * - вызвано ли новое меню просмотра расписания
         * - нажатие кнопки "НАЗАД" из меню выбора зала
         */
        if (isset($this->update['callback_query'])) {
            $content = array(
                'chat_id' => $this->update['callback_query']['message']['chat']['id'],
                'message_id' => $this->update['callback_query']['message']['message_id'],
                'text' => $this->renderView('telegram_bot/schedule.html.twig'),
                'parse_mode' => 'HTML',
                'reply_markup' => $inlineKeyBoard
            );
            $this->bot->editMessageText($content);
        } else {
            $content = array(
                'chat_id' => $this->update['message']['chat']['id'],
                'text' => $this->renderView('telegram_bot/schedule.html.twig'),
                'parse_mode' => 'HTML',
                'reply_markup' => $inlineKeyBoard
            );
            $this->bot->sendMessage($content);
        }
    }

    /**
     * Command: contact_with
     *
     * @throws OptimisticLockException
     */
    private function _contactWith($org_id, $_name = null, $_company = null, $_phone = null)
    {
        //&& (strlen($_phone) < 8 && strlen($_phone) > 16)
        //&& !preg_match("/(7|8)\d{10}/", $_phone, $matches)

        $isValid = true;
        if (isset($_name) && strlen($_name) > 50) {
            $isValid = false;
        } elseif (isset($_company) && strlen($_name) > 50) {
            $isValid = false;
        }
        if (isset($_phone)) {
            $f_phone = preg_replace("/\D/", '', $_phone);
            if (strlen($f_phone) < 8 || strlen($f_phone) > 16) {
                $isValid = false;
            }
            $_phone = $f_phone;
        }

        /** @var Organization $org */
        $org = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Organization')
            ->find($org_id);

        $template_parameters = array();
        $template_parameters['org_name'] = $org->getName();

        if (isset($_name, $_company, $_phone) && $isValid) {

            $template_parameters['name'] = $_name;
            $template_parameters['company'] = $_company;
            $template_parameters['phone'] = $_phone;
            $template_parameters['org'] = $org;

            $_TEXT = $this->renderView('telegram_bot/contact_with.html.twig', $template_parameters);

            $chat_id = isset($this->update['message']) ? $this->update['message']['chat']['id'] : $this->update['callback_query']['message']['chat']['id'];
            $em = $this->getDoctrine()->getManager();

            $sms_text = $this->renderView('telegram_bot/_contact_sms.html.twig', $template_parameters);
            $this->sms->setEntityClass('tg_connection');
            $this->sms->setEntityId($chat_id);
            /** @var User $user */
            foreach ($org->getUsers() as $user) {
                $log = new Logs();
                $log->setReaded(0);
                $log->setDate(new \DateTime());
                $log->setEntity('tg_connection');
                $log->setElementId($chat_id);
                $log->setEvent('{}');
                $em->persist($log);
                $em->flush($log);
                $msg = $this->sms->addMessage('cros2018_'.$log->getId(), $user->getUsername(), $sms_text);
                $log->setEvent(json_encode($msg));
                $em->persist($log);
                $em->flush($log);
            }
            $this->sms->send();

            $content = array(
                'chat_id' => $chat_id,
                //'message_id' => $this->update['callback_query']['message']['message_id'],
                'text' => $_TEXT,
                'parse_mode' => 'HTML'
            );
            $this->tsm->resetState($this->tgChat);
        } elseif (!$isValid) {
            $state = $this->tgChat->getState();

            if (!isset($state['_name'])) {
                $state['reply_type'] = 'contact_with';
                $state['_org_id'] = $org_id;
            } elseif (!isset($state['_company'])) {
                $template_parameters['name'] = $state['_name'];
            } elseif (!isset($state['_phone'])) {
                $template_parameters['name'] = $state['_name'];
                $template_parameters['company'] = $state['_company'];
            } else {
                throw new InvalidParameterException("All parameters must be defined.");
            }

            if ($isValid) {
                $this->tsm->updateState($this->tgChat, $state);
            }

            $content = array(
                'chat_id' => isset($this->update['message']) ? $this->update['message']['chat']['id'] : $this->update['callback_query']['message']['chat']['id'],
                //'message_id' => $this->update['callback_query']['message']['message_id'],
                'text' => $this->renderView('telegram_bot/contact_with.html.twig', $template_parameters),
                'parse_mode' => 'HTML',
                'reply_markup' => $this->bot->buildForceReply(true),
            );
        } else {
            $state = $this->tgChat->getState();

            if (!isset($_name)) {
                $state['reply_type'] = 'contact_with';
                $state['_org_id'] = $org_id;
            } elseif (!isset($_company)) {
                $template_parameters['name'] = $_name;
                $state['_name'] = $_name;
            } elseif (!isset($_phone)) {
                $template_parameters['name'] = $_name;
                $template_parameters['company'] = $_company;
                $state['_company'] = $_company;
            } else {
                throw new InvalidParameterException("All parameters must be defined.");
            }

            if ($isValid) {
                $this->tsm->updateState($this->tgChat, $state);
            }

            $content = array(
                'chat_id' => isset($this->update['message']) ? $this->update['message']['chat']['id'] : $this->update['callback_query']['message']['chat']['id'],
                //'message_id' => $this->update['callback_query']['message']['message_id'],
                'text' => $this->renderView('telegram_bot/contact_with.html.twig', $template_parameters),
                'parse_mode' => 'HTML',
                'reply_markup' => $this->bot->buildForceReply(true),
            );
        }

        $this->bot->sendMessage($content);
        //$this->bot->editMessageText($content);
    }

    /**
     * Command: show_halls
     */
    private function _showHalls($date)
    {
        /** @var LectureRepository $lectureRepo */
        $all_halls = $this->getDoctrine()->getRepository("AppBundle:Hall")->findAll();

        $option = array();
        /** @var Hall $hall */
        foreach ($all_halls as $hall) {
            $option[] = array(
                $this->bot->buildInlineKeyBoardButton(
                    $hall->getHallName(),
                    false,
                    'show_lectures:' . $date . ':' . $hall->getId() . ':1'
                )
            );
        }
        $option[] = array($this->bot->buildInlineKeyBoardButton("Все залы", false, 'show_lectures:' . $date . ':all:1'));
        $option[] = array($this->bot->buildInlineKeyBoardButton("НАЗАД", false, 'show_dates'));

        $text = $this->renderView('telegram_bot/schedule.html.twig', ['date' => $date]);
        $content = array(
            'chat_id' => $this->update['callback_query']['message']['chat']['id'],
            'message_id' => $this->update['callback_query']['message']['message_id'],
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $this->bot->buildInlineKeyBoard($option)
        );
        $this->bot->editMessageText($content);
    }

    /**
     * Command: show_lectures
     * @param $date
     * @param $hallId
     * @param $page
     * @param string|null $lecture_action
     * @param int|null $lecture_id
     * @param string|null $info_action
     * @return bool
     * @throws EntityNotFoundException
     */
    private function _showLectures($date, $hallId, $page, $lecture_action = null, $lecture_id = null, $info_action = null)
    {
        $buttons = array();
        if (isset($lecture_action, $lecture_id) && $lecture_action == 'info') {
            if (isset($info_action)) {
                switch ($info_action) {
                    case 'subscribe':
                        $this->tsm->subscribeLecture($this->tgChat, $lecture_id);
                        break;
                    case 'unsubscribe':
                        $this->tsm->unsubscribeLecture($this->tgChat, $lecture_id);
                        break;
                }
            }

            /**
             * Тут оформление сообщения с информацией по лекции
             */
            $lecture = $this->_findLecture($lecture_id);
            $text = $this->renderView('telegram_bot/lecture_info.html.twig', ['lecture' => $lecture]);

            if ($this->tgChat->getLectures()->contains($lecture)) {
                $buttons[] = array(
                    $this->bot->buildInlineKeyBoardButton(
                        "Отписаться",
                        false,
                        "show_lectures:$date:$hallId:$page:info:$lecture_id:unsubscribe"
                    )
                );
            } else {
                $buttons[] = array(
                    $this->bot->buildInlineKeyBoardButton(
                        "Подписаться",
                        false,
                        "show_lectures:$date:$hallId:$page:info:$lecture_id:subscribe"
                    )
                );
            }

            $buttons[] = array(
                $this->bot->buildInlineKeyBoardButton(
                    "НАЗАД",
                    false,
                    "show_lectures:$date:$hallId:$page:toggle:$lecture_id"
                )
            );

        } else {

            $text = $this->renderView('telegram_bot/schedule.html.twig', [
                'date' => $date,
                'hall' => ($hallId == 'all') ? 'все' : $this->_findHall($hallId)->getHallName()
            ]);

            /** @var LectureRepository $lectureRepo */
            $lectureRepo = $this->getDoctrine()->getRepository("AppBundle:Lecture");

            if ($hallId === 'all') {
                $lectures_query = $lectureRepo->findByHallIdNotNull($date);
                $lectures_count = count($lectures_query->getResult());
                $lectures =
                    $lectures_query
                        ->setFirstResult(self::LECTURES_ON_PAGE * ($page - 1))
                        ->setMaxResults(self::LECTURES_ON_PAGE)->getResult();
            } else {
                $_where = array('hallId' => $hallId);
                $_where['date'] = new \DateTime($date);
                $lectures = $lectureRepo->findBy(
                    $_where,
                    array('startTime' => 'ASC'),
                    self::LECTURES_ON_PAGE,
                    self::LECTURES_ON_PAGE * ($page - 1)
                );
                $lectures_count = count($lectureRepo->findBy($_where, array('startTime' => 'ASC')));
            }

            /**
             * Inline-кнопки лекций
             */
            /** @var Lecture $lecture */
            foreach ($lectures as $lecture) {
                $_id = $lecture->getId();

                if (isset($lecture_action, $lecture_id) && $lecture_id == $_id) {

                    switch ($lecture_action) {
                        /**
                         * Разворачиваем мини-меню для лекции, если оно вызвано
                         */
                        case 'toggle':
                            $_subscribed = $this->tgChat->getLectures()->contains($lecture);

                            $btn_text = $lecture->getStartTime()->format("H:i") . ' | ' . $lecture->getTitle();
                            $buttons[] = array(
                                $this->bot->buildInlineKeyBoardButton(
                                    $btn_text,
                                    false,
                                    "show_lectures:$date:$hallId:$page"
                                )
                            );

                            if ($_subscribed) {
                                $_btn = $this->bot->buildInlineKeyBoardButton(
                                    "Отписаться",
                                    false,
                                    "show_lectures:$date:$hallId:$page:unsubscribe:$lecture_id"
                                );
                            } else {
                                $_btn = $this->bot->buildInlineKeyBoardButton(
                                    "Подписаться",
                                    false,
                                    "show_lectures:$date:$hallId:$page:subscribe:$lecture_id"
                                );
                            }

                            $buttons[] = array(
                                $this->bot->buildInlineKeyBoardButton(
                                    "Подробнее",
                                    false,
                                    "show_lectures:$date:$hallId:$page:info:{$lecture->getId()}"
                                ),
                                $_btn
                            );
                            break;
                        /**
                         * Подписываемся
                         */
                        case 'subscribe':
                            $this->tsm->subscribeLecture(
                                $this->tgChat,
                                $lecture_id
                            );

                            $btn_text = $lecture->getStartTime()->format("H:i") . ' | ' . $lecture->getTitle();
                            $buttons[] = array(
                                $this->bot->buildInlineKeyBoardButton(
                                    $btn_text,
                                    false,
                                    "show_lectures:$date:$hallId:$page"
                                )
                            );

                            $buttons[] = array(
                                $this->bot->buildInlineKeyBoardButton(
                                    "Подробнее",
                                    false,
                                    "show_lectures:$date:$hallId:$page:info:{$lecture->getId()}"
                                ),
                                $this->bot->buildInlineKeyBoardButton(
                                    "Отписаться",
                                    false,
                                    "show_lectures:$date:$hallId:$page:unsubscribe:{$lecture->getId()}"
                                )
                            );
                            break;
                        /**
                         * Отписываемся
                         */
                        case 'unsubscribe':
                            $this->tsm->unsubscribeLecture(
                                $this->tgChat,
                                $lecture_id
                            );

                            $btn_text = $lecture->getStartTime()->format("H:i") . ' | ' . $lecture->getTitle();
                            $buttons[] = array(
                                $this->bot->buildInlineKeyBoardButton(
                                    $btn_text,
                                    false,
                                    "show_lectures:$date:$hallId:$page"
                                )
                            );

                            $buttons[] = array(
                                $this->bot->buildInlineKeyBoardButton(
                                    "Подробнее",
                                    false,
                                    "show_lectures:$date:$hallId:$page:info:{$lecture->getId()}"
                                ),
                                $this->bot->buildInlineKeyBoardButton(
                                    "Подписаться",
                                    false,
                                    "show_lectures:$date:$hallId:$page:subscribe:{$lecture->getId()}"
                                )
                            );
                            break;
                    }

                } else {
                    $buttons[] = array(
                        $this->bot->buildInlineKeyBoardButton(
                            $lecture->getStartTime()->format("H:i") . ' | ' . $lecture->getTitle(),
                            false,
                            "show_lectures:$date:$hallId:$page:toggle:{$lecture->getId()}"
                        )
                    );
                }
            }

            /**
             * Если лекций больше, чем можно выводить на страницу, то
             * - пишем номер страницы
             * - добавляем кнопки пагинации
             */
            if ($lectures_count > self::LECTURES_ON_PAGE) {
                $totalPages = round($lectures_count / self::LECTURES_ON_PAGE, 0,PHP_ROUND_HALF_UP);
                $text .= "\n".$this->renderView('telegram_bot/_paginator_text.html.twig', ['current_page' => $page, 'total_pages' => $totalPages]);

                if ($totalPages > 1) {
                    if ($page == 1) {
                        $buttons[] = array($this->bot->buildInlineKeyBoardButton(">>>", false, 'show_lectures:'.$date.':'.$hallId.':'.($page+1)));
                    } elseif ($page == $totalPages) {
                        $buttons[] = array($this->bot->buildInlineKeyBoardButton("<<<", false, 'show_lectures:'.$date.':'.$hallId.':'.($page-1)));
                    } else {
                        $buttons[] = array(
                            $this->bot->buildInlineKeyBoardButton("<<<", false, 'show_lectures:'.$date.':'.$hallId.':'.($page-1)),
                            $this->bot->buildInlineKeyBoardButton(">>>", false, 'show_lectures:'.$date.':'.$hallId.':'.($page+1))
                        );
                    }
                }
            }

            /**
             * Если по заданным параметрам докладов не найдено
             * выводим "Докладов не найдено" и кнопку "Назад"
             */
            if ($lectures_count == 0) {
                $text .= "\nДокладов не найдено";
            }

            /**
             * Кнопка "НАЗАД" для возврата к выбору зала, сохраняя выбор даты
             */
            $buttons[] = array($this->bot->buildInlineKeyBoardButton("НАЗАД", false, 'show_halls:'.$date));
        }

        $content = array(
            'chat_id' => $this->update['callback_query']['message']['chat']['id'],
            'message_id' => $this->update['callback_query']['message']['message_id'],
            'text' => ($text == '') ? 'Нет данных' : $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $this->bot->buildInlineKeyBoard($buttons)
        );
        $this->bot->editMessageText($content);
    }

    private function init_bot()
    {
        $bot_token = $this->container->getParameter('tg.bot.token');
        if (!$bot_token) {
            throw new \Exception("tg.bot.token is not set in parameters");
        }

        $this->bot = new Telegram($bot_token);
    }

    /**
     * @param $lectureId
     * @return Lecture
     * @throws EntityNotFoundException
     */
    private function _findLecture($lectureId)
    {
        $lecture = $this->getDoctrine()->getRepository('AppBundle:Lecture')->find($lectureId);
        if (!$lecture) {
            throw new EntityNotFoundException("Lecture not found.");
        }
        return $lecture;
    }

    /**
     * @param $hallId
     * @return Hall
     * @throws EntityNotFoundException
     */
    private function _findHall($hallId)
    {
        $hall = $this->getDoctrine()->getRepository('AppBundle:Hall')->find($hallId);
        if (!$hall) {
            throw new EntityNotFoundException("Hall not found.");
        }
        return $hall;
    }

    /**
     * @return TgChat
     * @throws EntityNotFoundException
     */
    private function _findTgChat()
    {
        $tgChatId = isset($this->update['message']) ? $this->update['message']['chat']['id'] : $this->update['callback_query']['message']['chat']['id'];

        $tgChat = $this->getDoctrine()->getRepository('AppBundle:TgChat')->findOneBy(['chatId' => $tgChatId]);
        if (!$tgChat) {
            $tgChat = new TgChat();
            $tgChat->setChatId($tgChatId);
            $this->getDoctrine()->getManager()->persist($tgChat);
            $this->getDoctrine()->getManager()->flush();
        }
        return $tgChat;
    }

    /**
     * Обработчик ошибок
     *
     * Отправляет сообщение о неполадке для пользователя
     * и расширенное сообщение с указанием ошибки в чат админа (Евгений Н.)
     *
     * @param \Exception $e
     * @return Response
     */
    private function _error($e)
    {
        if (isset($this->bot)) {
            $msg = "Что-то пошло не так. Сообщение об ошибке отправлено специалистам.";
            $chat_id = isset($this->update['message']) ? $this->update['message']['chat']['id'] : $this->update['callback_query']['message']['chat']['id'];

            // Сообщение об ошибке в чат администратора
            if ($chat_id == '285036678') {
                $msg .= "\n\n"."Error: ".$e->getMessage()." Line: ".$e->getLine()." in ".$e->getFile();
            }

            $content = array(
                'chat_id' => isset($this->update['message']) ? $this->update['message']['chat']['id'] : $this->update['callback_query']['message']['chat']['id'],
                'text' => $msg
            );
            $this->bot->sendMessage($content);
            return new Response('ok', 200);
        }

        /** @var Logger $logger */
        $logger = $this->get('monolog.logger');
        $logger->error("EROORROROOROROROROROROORRRRRRR: ".$e->getMessage());
    }

    private function _debug($data)
    {
        if ($this->container->getParameter('kernel.environment') == 'dev') {
            file_put_contents('/home/cros/www/var/logs/tg_bot.log', print_r($data, true), FILE_APPEND);
        }
    }

}

