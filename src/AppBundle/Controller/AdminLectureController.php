<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Hall;
use AppBundle\Entity\Lecture;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Google_Client;
use Google_Service_Sheets;

class AdminLectureController extends Controller
{
    
    /**
     * @Route("/admin/lectures", name="admin-lectures")
     *
     * @param Request $request
     *
     * @return object
     */
    public function indexAction(Request $request)
    {
        $lectureRepository = $this->getDoctrine()->getRepository('AppBundle:Lecture');
        
        $lectures = $lectureRepository->findAll();
        $tbody = '';
        foreach ($lectures as $row) {
            $tbody .= "<tr>";
            $tbody .= "<td>{$row->getDate()->format("d.m.Y")}</td>";
            $tbody .= "<td>{$row->getStartTime()->format("H:i")}</td>";
            $tbody .= "<td>{$row->getEndTime()->format("H:i")}</td>";
            $tbody .= "<td>{$row->getHall()}</td>";
            $tbody .= "<td>{$row->getSpeaker()}</td>";
            $tbody .= "<td>{$row->getCompany()}</td>";
            $tbody .= "<td>{$row->getModerator()}</td>";
            $tbody .= "<td>{$row->getTitle()}</td>";
            $tbody .= "<td>{$row->getTheses()}</td>";
            $tbody .= "</tr>";
        }

        return $this->render('admin/lectures/lectures.html.twig', array(
            'base_dir'  => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'tbody'     => $tbody
        ));

    }

    /**
     * @Route("/admin/lectures/refresh", name="admin-lectures-refresh")
     *
     * @param Request $request
     *
     * @return object
     *
     * @throws \Google_Exception
     */
    public function refreshAction(Request $request)
    {
        if ($request->getMethod() == 'POST')
        {
            $authKey = trim($request->get('authKey'));
            $clientSecretPath = $this->container->getParameter('lectures.sheets.client_secret');
            $credentialsPath = $this->container->getParameter('lectures.sheets.credentials_path');
            
            $client = new Google_Client();
            $client->setApplicationName("CROS-2018");
            $client->setScopes(implode(' ', array(Google_Service_Sheets::SPREADSHEETS_READONLY)));
            $client->setAuthConfig($clientSecretPath);
            $client->setAccessType('offline');
                
            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authKey);

            // Store the credentials to disk.
            if(!file_exists(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, json_encode($accessToken));
            
            return new JsonResponse(array(
                'result'    => 'success'
            ));
        }
        
        if ($request->getMethod() == 'AJAX')
        {
            $response_arr = array();

            /* We have credentials and client secret? */
            $clientSecretPath = $this->container->hasParameter('lectures.sheets.client_secret') ? $this->container->getParameter('lectures.sheets.client_secret') : false;
            $credentialsPath = $this->container->hasParameter('lectures.sheets.credentials_path') ? $this->container->getParameter('lectures.sheets.credentials_path') : false;

            /* Step 1: if both are null or false, return */
            if (!$clientSecretPath || !$credentialsPath) 
            {
                $response_arr['code'] = "NO_PARAMETERS";
                $response_arr['html'] = '<div class="alert alert-warning">Проверьте, указаны ли следующие <i>parameters</i> в <b>%kernel.root_dir%/config/services.yml</b>:'
                        . '<ul><li>lectures.sheets.client_secret</li><li>lectures.sheets.credentials_path</li></ul>'
                        . "<br><b>Пример</b>"
                        . "<pre>parameters:\n"
                        . "\t...\n"
                        . "\tlectures.sheets.client_secret: \"%kernel.root_dir%/google_sheets/google_client_secret.json\"\n"
                        . "\tlectures.sheets.credentials_path: \"%kernel.root_dir%/google_sheets/cros.google-sheets.credentials.json\"</pre>"
                        . "</div>";

                return new JsonResponse($response_arr);
            }
            elseif (!file_exists($clientSecretPath))
            {
                $response_arr['code'] = "NO_CLIENT_SECRET";
                $response_arr['html'] = "<div class='alert alert-warning'>Отсутствует файл $clientSecretPath";

                return new JsonResponse($response_arr);
            }
            else 
            {
                $client = new Google_Client();
                $client->setApplicationName("CROS-2018");
                $client->setScopes(implode(' ', array(Google_Service_Sheets::SPREADSHEETS_READONLY)));
                $client->setAuthConfig($clientSecretPath);
                $client->setAccessType('offline');
                
                if (file_exists($credentialsPath)) {
                    $accessToken = json_decode(file_get_contents($credentialsPath), true);
                } else {
                    // Request authorization from the user.
                    $authUrl = $client->createAuthUrl();
                    
                    return new JsonResponse(array(
                        'code'      => 'NO_CREDENTIALS',
                        'authUrl'   => $authUrl,
                        'html'      => "<div id='googleAuthModal' class='modal fade' role='dialog'>"
                                     . "<div class='modal-dialog'>"
                                     . "<div class='modal-content'>
                                        <div class='modal-header'>
                                          <button type='button' class='close' data-dismiss='modal'>&times;</button>
                                          <h4 class='modal-title'>Авторизация</h4>
                                        </div>
                                        <div class='modal-body'>
                                          <p>Перейдите по ссылке и получите код, чтобы разрешить сайту просмотр таблицы.</p>
                                          <p></p>
                                          <a href=".$authUrl." target='_blank'>Получить код</a>
                                          <p></p>
                                          <input name='authKey'/>
                                        </div>
                                        <div class='modal-footer'>
                                          <button type='button' class='btn btn-default' data-dismiss='modal'>Отмена</button>
                                          <button type='submit' onclick='updateAuthKey()' class='btn btn-success'>Отправить</button>
                                        </div>
                                      </div>

                                    </div>
                                  </div>"
                    ));
                }
                
                $client->setAccessToken($accessToken);

                // Refresh the token if it's expired.
                if ($client->isAccessTokenExpired()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
                }
                
                $service = new Google_Service_Sheets($client);
                
                /* WORK WITH SPREADSHEET HERE */
                $spreadsheetId = '1Olv8L8yGdhqXSWC3Eq3AOL0wqExKwDzkhGgwPrklF74';
                $range = 'Расписание докладов и КС!A:I';
                $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                $values = $response->getValues();
				unset($values[0]); $values = array_values($values);
		        $em = $this->getDoctrine()->getManager();

                $lectureRepository = $this->getDoctrine()->getRepository('AppBundle:Lecture');
                $hallRepository = $this->getDoctrine()->getRepository('AppBundle:Hall');


                $lecture_exist_ids = array();
        		/* SAVE ROWS */
		        foreach ($values as $row) {
        
		            $_date = new \DateTime($row[0]);
		            $_start_time = new \DateTime($row[1]);
		            $_end_time = new \DateTime($row[2]);
		            $_hall = isset($row[3]) ? $row[3] : '';
		            $_speaker = isset($row[4]) ? $row[4] : '';
		            $_company = isset($row[5]) ? $row[5] : '';
		            $_moderator = isset($row[6]) ? $row[6] : '';
		            $_title = isset($row[7]) ? $row[7] : '';
		            $_theses = isset($row[8]) ? $row[8] : '';

		            $lecture = $lectureRepository->findOneBy(array(
		                    'date' => $_date,
                            'startTime' => $_start_time,
                            'endTime' => $_end_time,
                            'title' => $_title
                        )
                    );
		            
		            if ($lecture)
		            {
						$lecture->setHall($_hall);
						$lecture->setSpeaker($_speaker);
						$lecture->setCompany($_company);
						$lecture->setModerator($_moderator);
						$lecture->setTheses($_theses);

                        $lecture_exist_ids[] = $lecture->getId();
		                
		                $em->persist($lecture);
		            }
		            else
		            {
		                $lecture = new Lecture();
				    
						$lecture->setDate($_date);
						$lecture->setStartTime($_start_time);
						$lecture->setEndTime($_end_time);
						$lecture->setHall($_hall);
						$lecture->setSpeaker($_speaker);
						$lecture->setCompany($_company);
						$lecture->setModerator($_moderator);
						$lecture->setTitle($_title);
						$lecture->setTheses($_theses);

                        $lecture_exist_ids[] = $lecture->getId();
				
		                $em->persist($lecture);
		            }
                    $em->flush();

		            /* Супер-проверка на НЕ {обед, кофе-брейк, etc.} */
		            if ($lecture->getSpeaker() !== '')
                    {
                        /** @var Hall $hall */
                        $hall = $hallRepository->findOneBy(array('hallName' => $lecture->getHall()));
                        if (!$hall)
                        {
                            $hall = new Hall();
                            $hall->setHallName($lecture->getHall());
                            $em->persist($hall);
                            $em->flush();
                        }

                        $hall_exist_ids[] = $hall->getId();

                        $lecture->setHallId($hall->getId());
                        $em->persist($lecture);
                        $em->flush();
                    }
	        	};

				/* DELETE ROWS */
                if (!empty($lecture_exist_ids)) {
                    foreach ($lectureRepository->findByNotInIds($lecture_exist_ids) as $one) {
                        $em->remove($one);
                    };
                    $em->flush();
                };
                if (!empty($hall_exist_ids)) {
                    foreach ($hallRepository->findByNotInIds($hall_exist_ids) as $one) {
                        $em->remove($one);
                    };
                    $em->flush();
                };

                return new JsonResponse(array(
                    'code' => 'SUCCESS',
                    'values' => $values
                ));
            }
        }
        else 
        {
            return new Response('', 405);
        }
    }


}

