<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Hall;
use AppBundle\Entity\Lecture;
use AppBundle\Repository\LectureRepository;
use AppBundle\Repository\TgChatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\TgChat;

class WebhookController extends Controller
{

    private $update;

	private $token = 'c2hhbWJhbGEyMykxMiUh';

	// setWebhook
	//https://api.telegram.org/bot527782633:AAFPLooKU0KwINR_CwRj7R-1Z_nHv9b5t0o/setWebhook?url=https://test-cros.nag.ru/webhook/update/c2hhbWJhbGEyMykxMiUh
	
	/**
	 * @Route("/webhook/update/{token}", name="webhook-update")
	 */
	public function updateAction($token)
	{
        /**
         * Проверяем get-переменную, с которой пришел запрос на вебхук
         */
		if ($this->token === $token) {
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
                        $this->_subscribe();
                        break;
                    default:
                        break;
                }
            }
            elseif (isset($this->update['callback_query']))
            {
                $data = trim($this->update['callback_query']['data']);

                $args = explode(":", $data);
                $cmd = array_shift($args);

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
     * Command: /privet
     */
    private function _privet()
    {
        $bot = $this->init_bot();
        $content = array('chat_id' => $this->update['message']['chat']['id'], 'text' => 'PRIVET');
        $bot->sendMessage($content);
    }

    /**
     * Command: /start
     */
    private function _start()
    {
        $bot = $this->init_bot();
        $chat_id = $this->update['message']['chat']['id'];
        $username = $this->update['message']['from']['username'];

        $text = "Привет, $username! Я бот конференции КРОС 2018.\n"
            ."Помогу следить за расписанием, быть в курсе событий.\n"
            ."Если понадоблюсь - жми МЕНЮ. Продуктивного тебе время провождения!";

        $options = array(
            array(),
            array($bot->buildKeyboardButton("МЕНЮ")),
            array()
        );
        $keyBoard = $bot->buildKeyBoard($options, false, true);

        $content = array('chat_id' => $chat_id, 'text' => $text, 'reply_markup' => $keyBoard);
        $bot->sendMessage($content);

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
        $bot = $this->init_bot();
        $chat_id = $this->update['message']['chat']['id'];

        $options = array();
        $keyBoard = $bot->buildKeyBoard($options, false, true);

        $content = array('chat_id' => $chat_id, 'text' => 'stop', 'reply_markup' => $keyBoard);
        $bot->sendMessage($content);

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
        $bot = $this->init_bot();

        $options = array(
            array($bot->buildKeyboardButton("Посмотреть расписание")),
            array($bot->buildKeyboardButton("Мое расписание")),
            array($bot->buildKeyboardButton("Уведомлять о начале докладов"))
        );
        $keyBoard = $bot->buildKeyBoard($options, true, true);

        $content = array(
            'chat_id' => $this->update['message']['chat']['id'],
            'text' => 'ping',
            'reply_markup' => $keyBoard
        );

        $bot->sendMessage($content);
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
    private function _subscribe()
    {

    }
    /**
     * Command: "Посмотреть расписание"
     */
    private function _showDates()
    {
        /** @var \Telegram $bot */
        $bot = $this->init_bot();

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT DISTINCT l.date FROM AppBundle\Entity\Lecture l ORDER BY l.date ASC');
        $dates = $query->getResult();

        file_put_contents('/home/cros/www/var/logs/tg_bot.log', "\n" . print_r($dates, true) . "\n\n", FILE_APPEND);

        $option = array();
        /** @var Hall $hall */
        foreach ($dates as $date) {
            $date_title = $date['date']->format("d.m.Y");
            $date_str = $date['date']->format("Y-m-d");
            $option[] = array($bot->buildInlineKeyBoardButton($date_title, false, 'show_halls:' . $date_str));
        }
        $inlineKeyBoard = $bot->buildInlineKeyBoard($option);

        $content = array(
            'chat_id' => $this->update['message']['chat']['id'],
            'text' => 'Выберите дату',
            'parse_mode' => 'Markdown',
            'reply_markup' => $inlineKeyBoard
        );
        $bot->sendMessage($content);

        /*
        $options = array(
            array(),
            array($bot->buildKeyboardButton("МЕНЮ")),
            array()
        );
        $keyBoard = $bot->buildKeyBoard($options, false, true);
        $mergeKeyboards = json_encode(
            array_merge(
                json_decode($inlineKeyBoard, true),
                json_decode($keyBoard, true)
            ),
            true);

        file_put_contents('/home/cros/www/var/logs/tg_bot.log', "\n" . print_r($mergeKeyboards, true) . "\n\n", FILE_APPEND);
        */

        /*
        $content = array(
            'chat_id' => $chat_id,
            'text' => '123',
            'reply_markup' => $keyBoard
        );
        $bot->sendMessage($content);
        */
    }

    /**
     * Command: show_halls
     */
    private function _showHalls($date)
    {
        /** @var \Telegram $bot */
        $bot = $this->init_bot();
        /** @var LectureRepository $lectureRepo */
        $hallRepo = $this->getDoctrine()->getRepository("AppBundle:Hall");

        // log
        file_put_contents('/home/cros/www/var/logs/tg_bot.log', "\nDate: $date\n", FILE_APPEND);

        $all_halls = $hallRepo->findAll();

        $option = array();
        /** @var Hall $hall */
        foreach ($all_halls as $hall) {
            $option[] = array(
                $bot->buildInlineKeyBoardButton(
                    $hall->getHallName(),
                    false,
                    'show_lectures:' . $date . ':' . $hall->getId()
                )
            );
        }
        $option[] = array($bot->buildInlineKeyBoardButton("Все залы", false, 'show_lectures:' . $date . ':all'));

        $content = array(
            'chat_id' => $this->update['callback_query']['message']['chat']['id'],
            'message_id' => $this->update['callback_query']['message']['message_id'],
            'text' => 'Выберите дату',
            'parse_mode' => 'Markdown',
            'reply_markup' => $bot->buildInlineKeyBoard($option)
        );
        $bot->sendMessage($content);
    }
    /**
     * Command: show_lectures
     */
    private function _showLectures($date, $hallId)
    {
        /** @var \Telegram $bot */
        $bot = $this->init_bot();
        /** @var LectureRepository $lectureRepo */
        $lectureRepo = $this->getDoctrine()->getRepository("AppBundle:Lecture");

        // log
        file_put_contents('/home/cros/www/var/logs/tg_bot.log', "\nHallId: $hallId\n", FILE_APPEND);

        if ($hallId === 'all')
        {
            $_where = array();
        }
        else
        {
            $_where = array('hallId' => $hallId);
        }
        $_where['date'] = $date;

        $lectures = $lectureRepo->findBy(
            $_where,
            array(
                'date' => 'ASC',
                'startTime' => 'ASC'
            )
        );

        $text = '';
        /** @var Lecture $lecture */
        foreach ($lectures as $lecture)
        {
            $text .= "\n".'<b>'.$lecture->getTitle().'</b>';
        }

        file_put_contents('/home/cros/www/var/logs/tg_bot.log', "\n" . print_r($all_halls, true) . "\n\n", FILE_APPEND);

        $option = array();
        /** @var Hall $hall */
        foreach ($all_halls as $hall) {
            $option[] = array($bot->buildInlineKeyBoardButton($hall->getHallName(), false, 'show_lectures:' . $hall->getId()));
        }
        $option[] = array($bot->buildInlineKeyBoardButton("Все залы", false, 'show_lectures:all'));

        $keyBoard = $bot->buildInlineKeyBoard($option);

        $content = array(
            'chat_id' => $this->update['message']['chat']['id'],
            'text' => 'ping',
            'parse_mode' => 'Markdown',
            'reply_markup' => $keyBoard
        );

        $content = array(
            'chat_id' => $this->update['callback_query']['message']['chat']['id'],
            'text' => ($text == '') ? 'Нет данных' : $text,
            'parse_mode' => 'HTML',
        );

        $bot->sendMessage($content);
    }


    /**
     * Initialize bot
     *
     * @return \Telegram
     */
    private function init_bot()
    {
        return new \Telegram('527782633:AAFPLooKU0KwINR_CwRj7R-1Z_nHv9b5t0o');
    }
	
}

