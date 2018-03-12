<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\TgChat;

class WebhookController extends Controller
{

    private $update;

	private $token = 'c2hhbWJhbGEyMykxMiUh';
	
	//https://api.telegram.org/bot527782633:AAFPLooKU0KwINR_CwRj7R-1Z_nHv9b5t0o/setWebhook?url=https://test-cros.nag.ru/webhook/update/c2hhbWJhbGEyMykxMiUh
	//
	
	
	/**
	 * @Route("/webhook/update/{token}", name="webhook-update")
	 */
	public function updateAction($token)
	{
        /**
         * Список доступных команд
         */
	    $availableCommands = array('/start', '/stop');

        /**
         * Проверяем get-переменную, с которой пришел запрос на вебхук
         */
		if ($this->token === $token) {
			$this->update = json_decode(file_get_contents('php://input'), true);

			// log
            file_put_contents('/home/cros/www/var/logs/tg_bot.log', "***\n[".date("d.m.Y H:i:s")."]\n".print_r($this->update, true)."\n\n", FILE_APPEND);

            /**
             * Определяем, что пришла команда, при том одна, возможно с аргументами
             */
            if (isset($this->update['message']['entities']) && count($this->update['message']['entities']) == 1
                && $this->update['message']['entities'][0]['type'] == 'bot_command')
            {
                /**
                 * Забираем весь текст сообщения, выделяем в нем команду
                 */
                $text = $this->update['message']['text'];
                $command = substr($text, $this->update['message']['entities'][0]['offset'], $this->update['message']['entities'][0]['length']);

                /**
                 * Проверяем, что команда есть в белом списке, он же список доступных команд
                 *
                 * TODO: А надо ли проверять по белому списку, если далее есть 'method_exists()'
                 */
                if (in_array($command, $availableCommands))
                {
                    /**
                     * Выделяем аргументы команды в массив
                     */
                    $args = explode(' ', $text);
                    $method = '_'.substr(array_shift($args), 1).'Command';

                    file_put_contents('/home/cros/www/var/logs/tg_bot.log', "***\n[".date("d.m.Y H:i:s")."]\n".'Method: '.$method.' | Args: '.implode(', ',$args)."\n\n", FILE_APPEND);

                    /**
                     * Есть ли обработчик для команды
                     */
                    if (method_exists($this, $method)) {
                        $this->{$method}($args);
                    }
                }
                else
                {
                    /**
                     * TODO: Method to invalid enter
                     */
                }
            }

            return new Response('ok', 200);
        }
		else
		{
			return new Response('', 403);
		}
	}

    /**
     * /privet command
     */
    private function _privetCommand($_chat_id = false)
    {
        $bot = $this->init_bot();

        $chat_id = $_chat_id ? $_chat_id : $this->update['chat']['id'];

        $content = array('chat_id' => $chat_id, 'text' => 'PRIVET');
        $bot->sendMessage($content);
    }

    /**
     * /start command
     */
    private function _startCommand($args)
    {
        $bot = $this->init_bot();
        $chat_id = isset($args['chat_id']) ? $args['chat_id'] : $this->update['message']['chat']['id'];
        $username = $this->update['message']['from']['username'];

        $text = "Привет, $username! Я бот конференции КРОС 2018.\n"
                ."Помогу следить за расписанием, быть в курсе событий.\n"
                ."Если понадоблюсь - жми МЕНЮ. Продуктивного тебе время провождения!";

        /**
         * TODO: Нарисовать кнопку МЕНЮ
         */
        $options = array(
            array($bot->buildKeyboardButton("МЕНЮ"))
        );
        $keyBoard = $bot->buildKeyBoard($options, false);

        /**
         * Собираем ответ в $content и отсылаем
         */
        $content = array('chat_id' => $chat_id, 'text' => $text, 'reply_markup' => $keyBoard);
        $bot->sendMessage($content);


        /**
         * Проверяем, есть ли чат в базе
         */
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('AppBundle:TgChat');

        $chat = $repo->findOneByChatId($chat_id);

        /**
         * Если чат найден, но был неактивен, активируем его
         */
        if ($chat && false === $chat->IsActive())
        {
            $chat->setIsActive(true);
        }
        /**
         * Если чат не найден, то создадим его
         */
        else
        {
            $chat = new TgChat();
            $chat->setChatId($chat_id);
            $chat->setIsActive(true);
        };
        $em->persist($chat);
        $em->flush();
    }



    /**
     * Initialize bot
     *
     * @return \Telegram
     */
    private function init_bot()
    {
        $bot = new \Telegram('527782633:AAFPLooKU0KwINR_CwRj7R-1Z_nHv9b5t0o');

        return $bot;
    }
	
}

