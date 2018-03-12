<?php

namespace AppBundle\Controller;

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

            $text = trim($this->update['message']['text']);

            switch ($text) {
                case '/start':
                    $this->_start();
                    break;
                case 'МЕНЮ':
                    $this->_menu();
                    break;
                case 'Посмотреть расписание':
                    $this->_showLectures();
                    break;
                case 'Мое расписание':
                    $this->_myLecturesCommand();
                    break;
                case 'Уведомлять о начале докладов':
                    $this->_subscribeCommand();
                    break;
                default:
                    break;
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
     * Command: "Посмотреть расписание"
     */
    private function _showLectures($page = 1)
    {
        /** @var \Telegram $bot */
        $bot = $this->init_bot();

        /** @var ArrayCollection $lectures */
        $lectureRepository = $this->getDoctrine()->getRepository("AppBundle:Lecture");

        $all_halls = $lectureRepository->findHall();
        $program = array();

        if (false)
        {

        }
        else
        {
            /** MISTAKE HERE **/
            /** @var  $lecture */
            foreach ($lectureRepository->findAll()->toArray() as $lecture)
            {
                $_day_key = $lecture->getDate()->format('d.m.Y');
                $_time_key = $lecture->getStartTime()->format("H:i")." - ".$lecture->getEndTime()->format("H:i");
                $_hall_key = $lecture->getHall();

                if (!$lecture->getSpeaker()) {
                    $program[$_day_key][$_time_key][Lecture::class::DEFAULT_HALL] = $lecture;
                } else {
                    $program[$_day_key][$_time_key][$_hall_key] = $lecture;
                    if (!in_array($_hall_key, $all_halls)) $all_halls[] = $_hall_key;
                };
            }
        }
    }

    /**
     * Command: "Мое расписание"
     */
    private function _myLecturesCommand()
    {

    }

    /**
     * Command: "Уведомлять о начале докладов"
     */
    private function _subscribeCommand()
    {

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

