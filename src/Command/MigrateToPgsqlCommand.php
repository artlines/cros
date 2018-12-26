<?php

namespace App\Command;

use App\Entity\Content\Info;
use App\Entity\Participating\ConferenceMember;
use App\Entity\Participating\ConferenceOrganization;
use App\Old\Entity\InfoToConf;
use App\Old\Entity\User;
use App\Old\Entity\Conference;
use App\Old\Entity\Organization;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MigrateToPgsqlCommand extends Command
{
    /** @var EntityManager */
    protected $mysqlManager;

    /** @var EntityManager */
    protected $pgsqlManager;

    protected static $defaultName = 'cros:migrate-to-pgsql';

    public function __construct(ContainerInterface $container)
    {
        $this->mysqlManager = $container->get('doctrine.orm.mysql_entity_manager');
        $this->pgsqlManager = $container->get('doctrine.orm.pgsql_entity_manager');

        parent::__construct();
    }

    protected function configure()
    {
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("--== START ==--");

        $this->pgsqlManager->beginTransaction();

        try {
            $this->migrateConferences();
            $this->migrateOrganizationsAndUsers();
            $this->migrateSiteInfo2018();
            //$this->migrateProgram2018();
            //$this->migrateSpeakers2018();
        } catch (\Exception $e) {
            $this->pgsqlManager->rollback();
            $output->writeln($e->getMessage());
        }

        $this->pgsqlManager->commit();

        $output->writeln(['', "--== END ==--"]);
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @throws \Exception
     */
    private function migrateConferences()
    {
        /** @var Conference[] $conferences */
        $conferences = $this->mysqlManager->getRepository('App\Old\Entity\Conference')->findAll();

        foreach ($conferences as $mysql_conference) {
            echo '$';

            $conf = new \App\Entity\Conference();
            $conf->setEventStart($mysql_conference->getStart());
            $conf->setEventFinish($mysql_conference->getFinish());
            $conf->setYear($mysql_conference->getYear());
            $conf->setRegistrationStart($mysql_conference->getRegistrationStart());
            $conf->setRegistrationFinish($mysql_conference->getRegistrationFinish());

            try {
                $this->pgsqlManager->persist($conf);
                $this->pgsqlManager->flush();
            } catch (\Exception $e) {
                throw new \Exception("ROLLBACK | Getting error while execute migrateConferences | {$e->getMessage()}");
            }
        }
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @throws \Exception
     */
    private function migrateOrganizationsAndUsers()
    {
        $prevConf = $this->getPreviousConference();

        /** @var Organization[] $organizations */
        $organizations = $this->mysqlManager->getRepository('App\Old\Entity\Organization')->findAll();

        foreach ($organizations as $mysql_org) {
            $_org_email = $mysql_org->getEmail();

            /**
             * Check that organization has users
             */
            if ($mysql_org->getUsers()->isEmpty()) {
                continue;
            }

            /**
             * Check for duplicate
             */
            $_existOrganization = $this->pgsqlManager
                ->getRepository('App\Entity\Participating\Organization')
                ->findOneBy(['email' => $_org_email]);
            if ($_existOrganization) {
                continue;
            }

            echo '@';

            $org = new \App\Entity\Participating\Organization();

            $org->setName($mysql_org->getName());
            $org->setEmail($_org_email);
            $org->setCity($mysql_org->getCity());
            $org->setRequisites($mysql_org->getRequisites());
            $org->setAddress($mysql_org->getAddress());
            $org->setIsActive($mysql_org->getIsActive());
            $org->setInn($mysql_org->getInn());
            $org->setKpp($mysql_org->getKpp());
            $org->setHidden($mysql_org->getHidden());

            $confOrg = new ConferenceOrganization();
            $confOrg->setOrganization($org);
            $confOrg->setConference($prevConf);
            $confOrg->setSponsor($mysql_org->getSponsor());

            try {
                $this->pgsqlManager->persist($org);
                $this->pgsqlManager->persist($confOrg);
                $this->pgsqlManager->flush();
            } catch (\Exception $e) {
                throw new \Exception("ROLLBACK | Getting error while execute migrateOrganizations | {$e->getMessage()}");
            }

            /**
             * Skip update the organization users if it is NAG
             */
            if ($org->getEmail() === 'web@nag.ru') {
                continue;
            }

            /** @var User $mysql_user */
            foreach ($mysql_org->getUsers()->toArray() as $mysql_user) {
                /**
                 * Validate phone
                 */
                $_phone = $mysql_user->getPhone();
                if (strlen($_phone) > 15 || strlen($_phone) < 8) {
                    continue;
                }

                /**
                 * Skip @nag.ru emails
                 */
                $_email = $mysql_user->getEmail();
                $_email_partial = explode('@', $_email);
                if (!isset($_email_partial[1]) || $_email_partial[1] === 'nag.ru') {
                    continue;
                }

                /**
                 * Check for duplicate
                 */
                $_existUser = $this->pgsqlManager
                    ->getRepository('App\Entity\Participating\User')
                    ->findOneBy(['email' => $_email]);
                if ($_existUser) {
                    continue;
                }

                echo '.';

                $user = new \App\Entity\Participating\User();
                $user->setOrganization($org);
                $user->setFirstName($mysql_user->getFirstName());
                $user->setLastName($mysql_user->getLastName());
                $user->setMiddleName($mysql_user->getMiddleName());
                $user->setPost($mysql_user->getPost());
                $user->setPhone($_phone);
                $user->setEmail($_email);
                $user->setIsActive($mysql_user->getIsActive());
                $user->setPassword($mysql_user->getPassword());
                $user->setTelegram($mysql_user->getTelegram());
                $user->setRoles($mysql_user->getRoles());
                $user->setNickname($mysql_user->getNickname());

                $_female = $mysql_user->getFemale();
                if ($_female === 1) {
                    $user->setSex(\App\Entity\Participating\User::SEX__WOMAN);
                } elseif ($_female === 0) {
                    $user->setSex(\App\Entity\Participating\User::SEX__MAN);
                }

                $confMember = new ConferenceMember();
                $confMember->setUser($user);
                $confMember->setConference($prevConf);

                try {
                    $this->pgsqlManager->persist($user);
                    $this->pgsqlManager->persist($confMember);
                    $this->pgsqlManager->flush();
                } catch (\Exception $e) {
                    throw new \Exception("ROLLBACK | Getting error while execute migrateOrganizations | {$e->getMessage()}");
                }
            }
        }
    }

    private function migrateSiteInfo2018()
    {
        /** @var \App\Entity\Conference[] $conferences */
        $conferences = $this->pgsqlManager->getRepository('App\Entity\Conference')->findAll();

        foreach ($conferences as $conference) {
            /** @var Conference $mysqlConf */
            $mysqlConf = $this->mysqlManager->getRepository('App\Old\Entity\Conference')
                ->findOneBy(['year' => $conference->getYear()]);

            /** @var InfoToConf[] $infoToConfs */
            $infoToConfs = $this->mysqlManager->getRepository('App\Old\Entity\InfoToConf')
                ->findBy(['conferenceId' => $mysqlConf->getId()]);

            foreach ($infoToConfs as $infoToConf) {
                echo '#';

                $info = $infoToConf->getInfo();

                $pgInfo = new Info();
                $pgInfo->setConference($conference);
                $pgInfo->setTitle($info->getTitle());
                $pgInfo->setAlias($info->getAlias());
                $pgInfo->setContent($info->getContent());

                try {
                    $this->pgsqlManager->persist($pgInfo);
                    $this->pgsqlManager->flush();
                } catch (\Exception $e) {
                    throw new \Exception("ROLLBACK | Getting error while execute migrateSiteInfo2018 | {$e->getMessage()}");
                }
            }
        }
    }

    // TODO:
    private function migrateSpeakers2018()
    {

    }

    // TODO:
    private function migrateProgram2018()
    {

    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @return \App\Entity\Conference
     */
    private function getPreviousConference()
    {
        return $this->pgsqlManager->getRepository('App\Entity\Conference')
            ->findOneBy(['year' => 2018]);
    }
}