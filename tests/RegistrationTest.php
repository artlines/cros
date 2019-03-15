<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationTest extends WebTestCase
{
    public function testSomething()
    {
        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/registration',
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
                                'kpp' => '1',
                                'requisites' => 'Полное наименование организации: 
ОГРН: 
Юридический адрес: 
Почтовый адрес: 
Банк: 
БИК: 
К/С: 
Р/С:',
                            ),
                        'notes' => '',
                        'ConferenceMembers' =>
                            array (
                                0 =>
                                    array (
                                        'user' =>
                                            array (
                                                'lastName' => 'Сюзев',
                                                'firstName' => 'Евгений',
                                                'middleName' => '',
                                                'sex' => '1',
                                                'phone' => '8(922)209-24-69',
                                                'email' => 'esuzev+2@gmail.com',
                                                'post' => 'sdadasd',
                                                'representative' => '1',
                                            ),
                                        'arrival' => '23.05.2019 10:00',
                                        'leaving' => '24.05.2019 12:00',
                                        'carNumber' => '',
                                        'RoomType' => '2',
                                        'neighbourhood' => '',
                                    ),
                            ),
                        '_token' => 'NcPl2fXVwX8NvQZn8FAcWT1UFOVd6di0W8s-CrIHkdk',
                        'save' => '',
                    ),
            )
        );

        dd($client->getResponse());
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Hello World', $crawler->filter('h1')->text());
    }
}
