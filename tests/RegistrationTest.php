<?php

namespace App\Tests;

use App\Entity\Participating\ConferenceOrganization;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationTest extends WebTestCase
{
    const TEST_CONFERENCE_ORGANIZATION_ID = 473;
    
    public function testNewRegOk()
    {
        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/test/registration',
            array(
                'conference_organization_form' =>
                    array(
                        'conference' => '272',
                        'organization' =>
                            array(
                                'name' => 'test.organization.name',
                                'city' => 'test.organization.city',
                                'address' => 'test.organization.address',
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
                                    'post' => 'test.user.0.post',
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

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $json = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('conferenceOrganization', $json);
        $this->assertArrayHasKey('id', $json['conferenceOrganization']);
        $this->assertArrayHasKey('organization', $json['conferenceOrganization']);
        $this->assertArrayHasKey('conference', $json['conferenceOrganization']);
    }

    public function testUserExist()
    {
        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/test/registration',
            array(
                'conference_organization_form' =>
                    array(
                        'conference' => '272',
                        'organization' =>
                            array(
                                'name' => 'test.organization.name',
                                'city' => 'test.organization.city',
                                'address' => 'test.organization.address',
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
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $error_json = json_decode($client->getResponse()->getContent(),true);
        $this->assertSame(['errors'=>['email' => 'Пользователь с такой почтой уже зарегистрирован']],$error_json);
    }

    public function testFindOrgByINN()
    {
        // Проверка что организация найдена по ИНН и КПП

        $kernel = self::bootKernel();
        $entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        /** @var ConferenceOrganization $testConferenceOrganization */
        $testConferenceOrganization = $entityManager
            ->getRepository(ConferenceOrganization::class)
            ->findOneById(self::TEST_CONFERENCE_ORGANIZATION_ID);
        ;

        $this->assertEquals(self::TEST_CONFERENCE_ORGANIZATION_ID, $testConferenceOrganization->getId() );

        $testConferenceOrganization->setFinish(false);
        $entityManager->persist($testConferenceOrganization);
        $entityManager->flush();
        //if (!);

        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/test/registration',
            array (
                'conference_organization_form' =>
                    array (
                        'conference' => $testConferenceOrganization->getConference()->getId(),
                        'organization' =>
                            array (
                                'name' => 'test.organization.name',
                                'city' => 'test.organization.city',
                                'address' => 'test.organization.address',
                                'inn' => $testConferenceOrganization->getOrganization()->getInn(),
                                'kpp' => $testConferenceOrganization->getOrganization()->getKpp(),
                                'requisites' => 'Тестовое наименование организации ОГРН:',
                            ),
                        'notes' => '',
                        'ConferenceMembers' => [
                            [
                                'user' => [
                                    'lastName' => 'Сюзев',
                                    'firstName' => 'Евгений',
                                    'middleName' => '',
                                    'sex' => '1',
                                    'phone' => '8(922)209-24-69',
                                    'email' => 'esuzev+4@test.com',
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

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $error_json = json_decode($client->getResponse()->getContent(),true);
        $this->assertSame([
            'conferenceOrganization' => [
                'id'           => $testConferenceOrganization->getId(),
                'organization' => [
                    'id'           => $testConferenceOrganization->getOrganization()->getId(),
                ],
                'conference' => [
                    'id'           => $testConferenceOrganization->getConference()->getId(),
                ],
            ]
        ],$error_json);

    }

    public function testFindOrgByINNfinish()
    {
        // Проверка что организация найдена по ИНН и КПП и нельзя регистрироваться

        $kernel = self::bootKernel();
        $entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        /** @var ConferenceOrganization $testConferenceOrganization */
        $testConferenceOrganization = $entityManager
            ->getRepository(ConferenceOrganization::class)
            ->findOneById(self::TEST_CONFERENCE_ORGANIZATION_ID);
        ;

        $this->assertEquals(self::TEST_CONFERENCE_ORGANIZATION_ID, $testConferenceOrganization->getId() );

        $testConferenceOrganization->setFinish(true);
        $entityManager->persist($testConferenceOrganization);
        $entityManager->flush();
        //if (!);

        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/test/registration',
            array (
                'conference_organization_form' =>
                    array (
                        'conference' => $testConferenceOrganization->getConference()->getId(),
                        'organization' =>
                            array (
                                'name' => 'test.organization.name',
                                'city' => 'test.organization.city',
                                'address' => 'test.organization.address',
                                'inn' => $testConferenceOrganization->getOrganization()->getInn(),
                                'kpp' => $testConferenceOrganization->getOrganization()->getKpp(),
                                'requisites' => 'Тестовое наименование организации ОГРН:',
                            ),
                        'notes' => '',
                        'ConferenceMembers' => [
                            [
                                'user' => [
                                    'lastName' => 'Сюзев',
                                    'firstName' => 'Евгений',
                                    'middleName' => '',
                                    'sex' => '1',
                                    'phone' => '8(922)209-24-69',
                                    'email' => 'esuzev+4@test.com',
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

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $error_json = json_decode($client->getResponse()->getContent(),true);
        $this->assertSame(['errors'=>['inn' => "Организация '".$testConferenceOrganization->getOrganization()->getName()."' уже зарегистрирована"]],$error_json);

    }
}
