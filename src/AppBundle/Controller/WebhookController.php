<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Hall;
use AppBundle\Entity\Lecture;
use AppBundle\Manager\TgChatManager;
use AppBundle\Repository\LectureRepository;
use AppBundle\Repository\TgChatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\QueryBuilder;
use Monolog\Logger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\TgChat;

class WebhookController extends Controller
{
    const LECTURES_ON_PAGE = 2;
    const MY_LECTURES_ON_PAGE = 2;

    /** @var \Telegram */
    private $bot;

    private $tgChat;

    /** @var array */
    private $update;

    /** @var TgChatManager */
    private $tsm;

    /** @var string */
	private $access_token = 'c2hhbWJhbGEyMykxMiUh';

	// setWebhook
	//https://api.telegram.org/bot527782633:AAFPLooKU0KwINR_CwRj7R-1Z_nHv9b5t0o/setWebhook?url=https://test-cros.nag.ru/webhook/update/c2hhbWJhbGEyMykxMiUh

    /**
     * @Route("/webhook/update/{token}", name="webhook-update")
     * @param $token
     * @return Response
     */
	public function update($token)
    {
        $this->tsm = $this->get('tg.chat.manager');
        if ($this->access_token === $token) {
            // Для очистки повисших запросов
            //return new Response('ok', 200);
            try {
                $this->init_bot();
                $this->update = json_decode(file_get_contents('php://input'), true);
                $this->_debug($this->update);
                $this->tgChat = $this->_findTgChat();
                $this->process();
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
     */
    private function process()
	{
        if (isset($this->update['message'])) {
            switch (trim($this->update['message']['text'])) {
                case '/start':
                    $this->_start();
                    break;
                case '/stop':
                    $this->_stop();
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
                default:
                    break;
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
            array($this->bot->buildKeyboardButton("Уведомлять о начале докладов"))
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
                'text' => '<strong>Уведомления</strong>' . "\n\n" .
                    'Вы хотите получать уведомления за 15 минут до начала докладов, на которые вы подписаны?',
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
                'text' => '<strong>Уведомления</strong>' . "\n\n" . '<i>' . $_status . '</i>',
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
            throw new \Exception("Bot token is not set in parameters.yml");
        }

        $this->bot = new \Telegram($bot_token);
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
            throw new EntityNotFoundException("TgChat not found.");
        }
        return $tgChat;
    }

    /**
     * Обработчик ошибок
     *
     * Сообщение о неполадке для пользователя
     * TODO: Сообщение отладки для администратора
     *
     * @param \Exception $e
     * @return Response
     */
    private function _error($e)
    {
        if (isset($this->bot)) {
            $msg = "Что-то пошло не так. Сообщение об ошибке отправлено специалистам.";
            $content = array(
                'chat_id' => isset($this->update['message']) ? $this->update['message']['chat']['id'] : $this->update['callback_query']['message']['chat']['id'],
                'text' => $msg //. $e->getMessage()
            );
            $this->bot->sendMessage($content);
            return new Response('ok', 200);
        }

        /** @var Logger $logger */
        $logger = $this->container->get('monolog.logger');
        $logger->error("EROORROROOROROROROROROORRRRRRR: ".$e->getMessage());
    }

    private function _debug($data)
    {
        // log
        file_put_contents('/home/cros/www/var/logs/tg_bot.log', print_r($data, true), FILE_APPEND);
    }
	
}

