<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Hall;
use AppBundle\Entity\Lecture;
use AppBundle\Repository\LectureRepository;
use AppBundle\Repository\TgChatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\TgChat;

class WebhookController extends Controller
{
    const BOT_TOKEN = '527782633:AAFPLooKU0KwINR_CwRj7R-1Z_nHv9b5t0o';

    const LECTURES_ON_PAGE = 1;

    /** @var \Telegram */
    private $bot;

    private $update;

	private $access_token = 'c2hhbWJhbGEyMykxMiUh';

	// setWebhook
	//https://api.telegram.org/bot527782633:AAFPLooKU0KwINR_CwRj7R-1Z_nHv9b5t0o/setWebhook?url=https://test-cros.nag.ru/webhook/update/c2hhbWJhbGEyMykxMiUh

    /**
     * @Route("/webhook/update/{token}", name="webhook-update")
     * @param $token
     * @return Response
     */
	public function updateAction($token)
	{
        $this->init_bot();

	    // Для очистки повисших запросов
        //return new Response('ok', 200);

	    /**
         * Проверяем get-переменную, с которой пришел запрос на вебхук
         */
		if ($this->access_token === $token) {
			$this->update = json_decode(file_get_contents('php://input'), true);

			// log
            file_put_contents('/home/cros/www/var/logs/tg_bot.log', "***\n[".date("d.m.Y H:i:s")."]\n".print_r($this->update, true)."\n\n", FILE_APPEND);

            if (isset($this->update['message']))
            {
                $text = trim($this->update['message']['text']);

                switch ($text) {
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
                        $this->_mySubscribes();
                        break;
                    case 'Уведомлять о начале докладов':
                        $this->_notifyMe();
                        break;
                    default:
                        break;
                }
            }
            elseif (isset($this->update['callback_query']))
            {
                $data = trim($this->update['callback_query']['data']);

                $args = explode(":", $data);
                $cmd = $args[0];

                // log
                $this->_debug($args);

                switch ($cmd) {
                    case 'show_halls':
                        $this->_showHalls($args[1]);
                        break;
                    case 'show_lectures':
                        $this->_showLectures($args[1], $args[2], $args[3]);
                        break;
                    default:
                        break;
                }
            }
            else
            {

            }

            return new Response('ok', 200);
        }
		else
		{
			return new Response('', 403);
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
            ."Если понадоблюсь - жми МЕНЮ. Продуктивного тебе время провождения!";

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

        if ($chat)
        {
            if (false === $chat->getIsActive())
            {
                $chat->setIsActive(true);
                $em->persist($chat);
                $em->flush();
            }
        }
        else
        {
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
    private function _mySubscribes()
    {

    }

    /**
     * Command: "Уведомлять о начале докладов"
     */
    private function _notifyMe($flag = null)
    {
        /**
         * Ищем чат в базе, смотрим на флаг уведомлений
         */


        /**
         * Показываем меню/статус уведомлений
         */
        if (is_null($flag)) {
            $content = array(
                'chat_id' => $this->update['message']['chat']['id'],
                'text' => '<strong>Меню уведомлений</strong>' . "\n\n" . 'Выберите дату',
                'parse_mode' => 'HTML'
            );
            $this->bot->sendMessage($content);
        }

        /**
         * Включаем/отключаем уведомления
         */
        if ($flag) {

        } else {

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

        //log
        $this->_debug($dates);

        $option = array();
        /** @var Hall $hall */
        foreach ($dates as $date) {
            $date_title = $date['date']->format("d.m.Y");
            $date_str = $date['date']->format("Y-m-d");
            $option[] = array($this->bot->buildInlineKeyBoardButton($date_title, false, 'show_halls:' . $date_str));
        }
        $inlineKeyBoard = $this->bot->buildInlineKeyBoard($option);

        $content = array(
            'chat_id' => $this->update['message']['chat']['id'],
            'text' => '<strong>Просмотр расписания</strong>' . "\n\n" . 'Выберите дату',
            'parse_mode' => 'HTML',
            'reply_markup' => $inlineKeyBoard
        );
        $this->bot->sendMessage($content);
    }

    /**
     * Command: show_halls
     */
    private function _showHalls($date)
    {
        /** @var LectureRepository $lectureRepo */
        $all_halls = $this->getDoctrine()->getRepository("AppBundle:Hall")->findAll();

        // log
        $this->_debug(['all_halls' => $all_halls]);

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

        $text = "<strong>Просмотр расписания</strong>\n\n<strong>Выбранная дата:</strong> $date\n\n" . 'Выберите зал';
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
     */
    private function _showLectures($date, $hallId, $page)
    {
        $text = "<strong>Просмотр расписания</strong>\n\n".
                "<strong>Выбранная дата:</strong> $date\n".
                "<strong>Зал: </strong>";

        /** @var LectureRepository $lectureRepo */
        $lectureRepo = $this->getDoctrine()->getRepository("AppBundle:Lecture");

        if ($hallId === 'all') {
            $lectures_query = $lectureRepo->findByHallIdNotNull($date);
            $lectures_count = count($lectures_query->getResult());
            $lectures =
                $lectures_query
                    ->setFirstResult(self::LECTURES_ON_PAGE * ($page - 1))
                    ->setMaxResults(self::LECTURES_ON_PAGE)->getResult();
            $text .= 'все' . "\n";
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


            $hallName = $this->getDoctrine()->getRepository('AppBundle:Hall')->find($hallId)->getHallName();
            $text .= $hallName . "\n";
        }


        /**
         * Inline-кнопки лекций
         */
        $option = array();
        /** @var Lecture $lecture */
        foreach ($lectures as $lecture) {
            $option[] = array(
                $this->bot->buildInlineKeyBoardButton(
                    $lecture->getStartTime()->format("H:i") . ' | ' . $lecture->getTitle(),
                    false,
                    'lecture:' . $lecture->getId()
                )
            );
        }

        /**
         * Если лекций больше, чем можно выводить на страницу, то
         * - пишем номер страницы
         * - добавляем кнопки пагинации
         */
        if ($lectures_count > self::LECTURES_ON_PAGE) {
            $totalPages = round($lectures_count / self::LECTURES_ON_PAGE, PHP_ROUND_HALF_UP);
            $text .= "\n".$this->_renderPaginatorText($page, $totalPages);

            if ($totalPages > 1) {
                if ($page == 1) {
                    $option[] = array($this->bot->buildInlineKeyBoardButton(">>>", false, 'show_lectures:'.$date.':'.$hallId.':'.($page+1)));
                } elseif ($page == $totalPages) {
                    $option[] = array($this->bot->buildInlineKeyBoardButton("<<<", false, 'show_lectures:'.$date.':'.$hallId.':'.($page-1)));
                } else {
                    $option[] = array(
                        $this->bot->buildInlineKeyBoardButton("<<<", false, 'show_lectures:'.$date.':'.$hallId.':'.($page-1)),
                        $this->bot->buildInlineKeyBoardButton(">>>", false, 'show_lectures:'.$date.':'.$hallId.':'.($page+1))
                    );
                }
            }
        }

        /**
         * Если по заданным параметрам лекций не найдено
         * выводим "Лекций не найдено" и кнопку "Назад"
         */
        if ($lectures_count == 0) {
            $text .= "\nЛекций не найдено";
        }

        /**
         * Кнопка "НАЗАД" для возврата к выбору зала, сохраняя выбор даты
         */
        $option[] = array($this->bot->buildInlineKeyBoardButton("НАЗАД", false, 'show_halls:'.$date));

        $content = array(
            'chat_id' => $this->update['callback_query']['message']['chat']['id'],
            'message_id' => $this->update['callback_query']['message']['message_id'],
            'text' => ($text == '') ? 'Нет данных' : $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $this->bot->buildInlineKeyBoard($option)
        );
        $this->bot->editMessageText($content);
    }


    private function init_bot()
    {
        $this->bot = new \Telegram(self::BOT_TOKEN);
    }



    private function _debug($data)
    {
        // log
        file_put_contents('/home/cros/www/var/logs/tg_bot.log', print_r($data, true), FILE_APPEND);
    }



    private function _renderPaginatorText($cur_page, $count_page)
    {
        return "<i>Страница $cur_page из $count_page</i>";
    }
	
}

