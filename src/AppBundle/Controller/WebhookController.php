<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class WebhookController extends Controller
{
	private $token = 'c2hhbWJhbGEyMykxMiUh';
	
	
	/**
	 * @Route("/webhook/update/{token}", name="webhook-update")
	 */
	public function updateAction($token, Request $request)
	{
		if ($this->token === $request->get('token')) {
			$input = json_decode(file_get_contents('php://input'), true);


			
			// log $input to ~/www/var/logs/tg_bot.log
			file_put_contents('/home/cros/www/var/logs/tg_bot.log', "***\n[".date("d.m.Y H:i:s")."]\n".print_r($input, true)."\n\n", FILE_APPEND);

			

			return new Response('Success', 200);
		}
		else
		{
			return new Response('', 403);
		};
	}
	
	/**
	 * @Route("/admin/tg")
	 */
	public function testAction($token, Request $request)
	{
		
	}
	
}

