<?php

namespace App\Tests;

use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\Organization;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class RegistrationTest
 * @package App\Tests
 */
class RegistrationTest extends WebTestCase
{
    /**
     * Идентификатор организации с которой проводятся тесты
     */
    const TEST_CONFERENCE_ORGANIZATION_ID = 473;

    /**
     * Проверка усакшности регистрации на открытую конференцию
     */
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

    /**
     * Проверка дубликата пользователя
     */
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

    /**
     * Проверка что организация найдена по ИНН и КПП
     */
    public function testFindOrgByINN()
    {

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
        dump($client->getResponse()->getContent());
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

    /**
     * Проверка что организация найдена по ИНН и КПП и нельзя регистрироваться
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testFindOrgByInnFinish()
    {

        $kernel = self::bootKernel();
        /** @var EntityManager $entityManager */
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
        dump($client->getResponse()->getContent());
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $error_json = json_decode($client->getResponse()->getContent(),true);
        $this->assertSame(['errors'=>['inn' => "Организация 'test.organization.name' уже зарегистрирована"]],$error_json);

    }

    /**
     * Проверка обновления организации и доабвления её на конференцию.
     * Когда организация есть, а заявки на конференцию нет
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testFindOrgNoCo()
    {

        $kernel = self::bootKernel();
        /** @var EntityManager $entityManager */
        $entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        /** @var ConferenceOrganization $testConferenceOrganization */
        $testConferenceOrganization = $entityManager
            ->getRepository(ConferenceOrganization::class)
            ->findOneById(self::TEST_CONFERENCE_ORGANIZATION_ID);
        ;

        $this->assertEquals(self::TEST_CONFERENCE_ORGANIZATION_ID, $testConferenceOrganization->getId() );

        $conference_id = $testConferenceOrganization->getConference()->getId();
        // Удаляем ConferenceOrganization что бы убедиться в создании новой
//        ->remove();
//        foreach($testConferenceOrganization->getConferenceMembers() as $iCM){
//            //$entityManager->remove($iCM);
//        }
//        $entityManager->remove($testConferenceOrganization);
        $inn = '9999999';
        $kpp = '99999998';
        $organization = $entityManager->getRepository( Organization::class )
            ->findOneBy([
                'inn' => $inn,
                'kpp' => $kpp,
            ]);
        if (!$organization ) {
            $organization = new Organization();
            $organization->setname('test.name')
                ->setInn('9999999')
                ->setKpp('99999998');
        }
        $entityManager->persist($organization);
        $entityManager->flush();
        $inn = $testConferenceOrganization->getOrganization()->getInn();
        $kpp = $testConferenceOrganization->getOrganization()->getKpp();
        //if (!);

        $client = static::createClient();
        $crawler = $client->request(
            'POST',
            '/test/registration',
            [
                'conference_organization_form' => [
                    'conference' => $conference_id,
                    'organization' => [
                            'name' => 'test.organization.name',
                            'city' => 'test.organization.city',
                            'address' => 'test.organization.address',
                            'inn' => $inn,
                            'kpp' => $kpp,
                            'requisites' => 'Тестовое наименование организации ОГРН:',
                        ],
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
                ],
            ]
        );
        $organization = $entityManager->merge($organization);
        //dump($organization);
        $entityManager->remove($organization);
        $entityManager->flush();

        dump($client->getResponse()->getContent());
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $error_json = json_decode($client->getResponse()->getContent(),true);
        $this->assertSame(['errors'=>['inn' => "Организация 'test.organization.name' уже зарегистрирована"]],$error_json);

    }

}
