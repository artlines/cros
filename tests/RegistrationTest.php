<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationTest extends WebTestCase
{
    public function testNewRegOk()
    {
        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/test/registration',
            array (
                'conference_organization_form' =>
                    array (
                        'conference' => '272',
                        'organization' =>
                            array (
                                'name' => 'fghfghgh',
                                'city' => 'fghfhfh',
                                'address' => 'xcvxcvxcv',
                                'inn' => '45020130891',
                                'kpp' => '4',
                                'requisites' => 'Тестовое наименование организации ОГРН:',
                            ),
                        'notes' => '',
                        'ConferenceMembers' => [
                            0 => [
                                'user' => [
                                    'lastName' => 'Сюзев',
                                    'firstName' => 'Евгений',
                                    'middleName' => '',
                                    'sex' => '1',
                                    'phone' => '8(922)209-24-69',
                                    'email' => 'esuzev+3@test.com',
                                    'post' => 'sdadasd',
                                    'representative' => '1',
                                ],
                                'arrival' => '23.05.2019 10:00',
                                'leaving' => '24.05.2019 12:00',
                                'carNumber' => '',
                                'RoomType' => '2',
                                'neighbourhood' => '',
                            ],
                        ],
                        'save' => '',
                    ),
            )
        );

//        dd($client->getResponse());
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $error_json = json_decode($client->getResponse()->getContent(),true);
//        dd($client->getResponse()->getContent());
        //$this->assertSame([],$error_json);

        $this->assertContains('Ваша заявка принята',
            $crawler
                ->filter('div.container p')
                ->text());
    }

    public function testUserExist()
    {
        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/test/registration',
            array (
                'conference_organization_form' =>
                    array (
                        'conference' => '272',
                        'organization' =>
                            array (
                                'name' => 'fghfghgh',
                                'city' => 'fghfhfh',
                                'address' => 'xcvxcvxcv',
                                'inn' => '45020130891',
                                'kpp' => '4',
                                'requisites' => 'Тестовое наименование организации ОГРН:',
                            ),
                        'notes' => '',
                        'ConferenceMembers' => [
                            0 => [
                                'user' => [
                                    'lastName' => 'Сюзев',
                                    'firstName' => 'Евгений',
                                    'middleName' => '',
                                    'sex' => '1',
                                    'phone' => '8(922)209-24-69',
                                    'email' => 'esuzev+2@test.com',
                                    'post' => 'sdadasd',
                                    'representative' => '1',
                                ],
                                'arrival' => '23.05.2019 10:00',
                                'leaving' => '24.05.2019 12:00',
                                'carNumber' => '',
                                'RoomType' => '2',
                                'neighbourhood' => '',
                            ],
                        ],
                        'save' => '',
                    ),
            )
        );

//        dd($client->getResponse());
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $error_json = json_decode($client->getResponse()->getContent(),true);
//        dd($client->getResponse()->getContent());
        $this->assertSame(['email' => 'Пользователь с такой почтой уже зарегистрирован'],$error_json);

//        $this->assertContains('Ваша заявка принята',
//            $crawler
//                ->filter('div.container p')
//                ->text());
    }
}
