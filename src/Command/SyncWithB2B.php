<?php

namespace App\Command;

use App\Entity\Participating\Organization;
use App\Entity\Participating\User;
use App\Repository\OrganizationRepository;
use App\Repository\UserRepository;
use App\Service\B2BApi;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncWithB2B extends Command
{
    const CROS_SERVICE_SKU  = 'NAG-cros2019';
    const CROS_YEAR         = 2019;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var B2BApi */
    protected $b2bApi;

    /** @var LoggerInterface */
    protected $logger;

    /** @var InputInterface */
    protected $input;

    /** @var OutputInterface */
    protected $output;

    /**
     * SyncWithB2B constructor.
     * @param string|null $name
     * @param EntityManagerInterface $em
     * @param B2BApi $b2bApi
     * @param LoggerInterface $logger
     */
    public function __construct(string $name = null, EntityManagerInterface $em, B2BApi $b2bApi, LoggerInterface $logger)
    {
        $this->em       = $em;
        $this->b2bApi   = $b2bApi;
        $this->logger   = $logger;

        parent::__construct($name);
    }

    public function configure()
    {
        $this
            ->setName('app:sync-with-b2b')
            ->setDescription('Synchronize with B2B');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input    = $input;
        $this->output   = $output;

        $output->writeln("[START]");

        // sync contractors
        //$this->_syncContractors();

        // sync users
        $this->_syncUsers();

        // sync ties

        // sync invoices

        $output->writeln("[END]");
    }

    /**
     * Sync contractors
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    private function _syncContractors()
    {
        /** @var OrganizationRepository $organizationRepo */
        $organizationRepo = $this->em->getRepository(Organization::class);
        $organizations = $organizationRepo->findWithoutB2bGuidByConferenceYear(self::CROS_YEAR);
        $this->log("Found ".count($organizations)." organizations without b2b_guid which will be synchronized.");

        /** @var Organization $organization */
        foreach ($organizations as $organization) {
            $_title = $organization->getName();
            $_inn = $organization->getInn();
            $_kpp = $organization->getKpp();

            $response = $this->b2bApi->findContractorByInnKpp($_inn, $_kpp);

            if ($response['http_code'] === 200) {
                $b2bGuid = $response['data']['fixed_guid'];
                $organization->setB2bGuid($b2bGuid);
                $this->em->persist($organization);
                $this->em->flush();
                $this->log('Update organization (ID: '.$organization->getId().'). Set b2b_guid `'.$b2bGuid.'`.');
            } elseif ($response['http_code'] === 404) {
                $this->log('Contractor to organization (ID: '.$organization->getId().') not found on B2B. Error: '.$response['data'].'. Creating...');
                $createResponse = $this->b2bApi->createContractor($_title, $_inn, $_kpp);
                if ($createResponse['http_code'] === 200) {
                    $b2bGuid = $createResponse['data']['fixed_guid'];
                    $this->log('Contractor to organization (ID: '.$organization->getId().') created on B2B. '
                        .'Return and set b2b_guid `'.$b2bGuid.'`.');
                    $organization->setB2bGuid($b2bGuid);
                    $this->em->persist($organization);
                    $this->em->flush();
                } else {
                    $this->log('Error while trying to create contractor to organization (ID: '.$organization->getId().') on B2B. Error: '.$createResponse['data']);
                }
            } else {
                $this->log('Error while trying to found contractor to organization (ID: '.$organization->getId().') on B2B. Error: '.$response['data']);
            }

            usleep(500000);
        }
    }

    private function _syncUsers()
    {
        /** @var UserRepository $userRepo */
        $userRepo = $this->em->getRepository(User::class);
        $users = $userRepo->findWithoutB2bGuidByConferenceYear(self::CROS_YEAR, TRUE);
        $this->log("Found ".count($users)." representative users without b2b_guid which will be synchronized.");

        /** @var User $user */
        foreach ($users as $user) {
            $_email = $user->getEmail();
            $_fio = $user->getFullName();

            $response = $this->b2bApi->findUserByEmail($_email);

            if ($response['http_code'] === 200) {
                $b2bGuid = $response['data']['fixed_guid'];
                $user->setB2bGuid($b2bGuid);
                $this->em->persist($user);
                $this->em->flush();
                $this->log('Update user (ID: '.$user->getId().'). Set b2b_guid `'.$b2bGuid.'`.');
            } elseif ($response['http_code'] === 404) {
                $this->log('User to user (ID: '.$user->getId().') not found on B2B. Error: '.$response['data'].'. Creating...');
                $createResponse = $this->b2bApi->createUser($_email, $_fio);
                if ($createResponse['http_code'] === 200) {
                    $b2bGuid = $createResponse['data']['fixed_guid'];
                    $this->log('User to user (ID: '.$user->getId().') created on B2B. '
                        .'Return and set b2b_guid `'.$b2bGuid.'`.');
                    $user->setB2bGuid($b2bGuid);
                    $this->em->persist($user);
                    $this->em->flush();
                } else {
                    $this->log('Error while trying to create user to user (ID: '.$user->getId().') on B2B. Error: '.$createResponse['data']);
                }
            } else {
                $this->log('Error while trying to found user to user (ID: '.$user->getId().') on B2B. Error: '.$response['data']);
            }

            usleep(500000);
        }
    }

    protected function log($msg, array $data = [])
    {
        $this->output->writeln($msg);
        $this->logger->notice("[b2b-sync] $msg", $data);
    }
}