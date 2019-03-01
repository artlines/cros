<?php

namespace App\Command;

use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\Invoice;
use App\Entity\Participating\Organization;
use App\Entity\Participating\User;
use App\Repository\ConferenceOrganizationRepository;
use App\Repository\InvoiceRepository;
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

        // sync contractors with b2b_guid for valid inn kpp
        $this->_syncContractorsValid();

        // sync contractors
        $this->_syncContractors();

        // sync users
        $this->_syncUsers();

        // sync ties
        $this->_syncUsersToContractor();

        // sync invoices status
        $this->_checkInvoicesStatus();

        // sync invoices
        $this->_checkAndMakeInvoices();

        $output->writeln("[END]");
    }

    /**
     * Check and set contractor `invalid` flag to organization `invalid_inn_kpp` flag
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    public function _syncContractorsValid()
    {
        $this->output->writeln("==========");

        /** @var OrganizationRepository $organizationRepo */
        $organizationRepo = $this->em->getRepository(Organization::class);
        $organizations = $organizationRepo->findNotInnKppCheckedWithB2bGuid();
        $this->log("Found ".count($organizations)." organizations with b2b_guid which will be checked for valid requisites.");

        foreach ($organizations as $organization) {
            $b2bGuid = $organization->getB2bGuid();
            $response = $this->b2bApi->findContractorByGuid($b2bGuid);

            if ($response['http_code'] !== 200) {
                $this->log("Catch error while getting contractor info from B2B by guid `$b2bGuid`: {$response['data']}. Skipped it!");
                continue;
            }

            $isInvalid = $response['data']['invalid'];

            if (is_bool($isInvalid)) {
                $organization->setInvalidInnKpp($isInvalid);
                $this->em->persist($organization);
                $this->em->flush();

                $this->log('Update organization (ID: '.$organization->getId().'). Set invalidInnKpp '.
                    ($isInvalid ? 'true' : 'false').'.');
            }
        }
    }

    /**
     * Sync contractors
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    private function _syncContractors()
    {
        $this->output->writeln("==========");

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
        }
    }

    /**
     * Sync users
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    private function _syncUsers()
    {
        $this->output->writeln("==========");

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
        }
    }

    /**
     * Make ties between users and contractors
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    private function _syncUsersToContractor()
    {
        $this->output->writeln("==========");

        /** @var ConferenceOrganizationRepository $conferenceOrganizationRepo */
        $conferenceOrganizationRepo = $this->em->getRepository(ConferenceOrganization::class);
        $conferenceOrganizations = $conferenceOrganizationRepo->findWhereInnKppNotInvalidAndB2bGuidExist(self::CROS_YEAR);
        $this->log("Found ".count($conferenceOrganizations)." conference organizations which will be synchronized.");

        /** @var ConferenceOrganization $conferenceOrganization */
        foreach ($conferenceOrganizations as $conferenceOrganization) {
            $organization = $conferenceOrganization->getOrganization();
            $org_b2bGuid = $organization->getB2bGuid();

            $this->output->writeln("*");
            $this->log("[Process organization (ID: ".$organization->getId().") with b2b_guid `$org_b2bGuid`.]");

            $conferenceMembers = $conferenceOrganization->getConferenceMembers();

            $crosUsers = [];
            foreach ($conferenceMembers as $conferenceMember) {
                $user = $conferenceMember->getUser();
                $user_b2bGuid = $user->getB2bGuid();

                if ($user->isRepresentative() && $user_b2bGuid) {
                    $this->log("Found participating user from organization (ID: ".$organization->getId().") with b2b_guid `$user_b2bGuid`.");
                    $crosUsers[] = $user_b2bGuid;
                }
            }

            if (empty($crosUsers)) {
                $this->log("Organization (ID: ".$organization->getId().") has not participating users. Skipped it!");
                continue;
            }

            $b2bUsersResponse = $this->b2bApi->getContractorUsers($org_b2bGuid);
            if ($b2bUsersResponse['http_code'] !== 200) {
                $this->log("Catch error while getting contractor users from B2B to Organization. Skipped it!", [
                    'id'        => $organization->getId(),
                    'b2b_guid'  => $organization->getB2bGuid(),
                    'error'     => $b2bUsersResponse['data'],
                ]);
                continue;
            }

            $b2bUsers = $b2bUsersResponse['data']['users_guids'];
            $needAddUsers = array_diff($crosUsers, $b2bUsers);

            if (empty($needAddUsers)) {
                $this->log("Organization (ID: ".$organization->getId().") doesn't need to add users to B2B. Skipped it!");
                continue;
            }

            $updateResponse = $this->b2bApi->updateContractorUsers($org_b2bGuid, ['users_guids' => $needAddUsers]);
            if ($updateResponse['http_code'] !== 200) {
                $this->log("Catch error while update contractor users on B2B. Skipped it!", [
                    'id'                => $organization->getId(),
                    'b2b_guid'          => $organization->getB2bGuid(),
                    'need_add_users'    => $needAddUsers,
                    'error'             => $b2bUsersResponse['data'],
                ]);
                continue;
            }

            $this->log("Updated contractor users for Organization (ID: ".$organization->getId().") with b2b_guid `$org_b2bGuid`.");
        }
    }

    /**
     * Check invoice status
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    private function _checkInvoicesStatus()
    {
        /** @var InvoiceRepository $invoiceRepo */
        $invoiceRepo = $this->em->getRepository(Invoice::class);
        $invoices = $invoiceRepo->getWithOrderGuidToSync();
        $this->log("Found ".count($invoices)." invoices to sync.");

        foreach ($invoices as $invoice) {
            $guid = $invoice->getOrderGuid();
            $this->log("Check Invoice (ID: {$invoice->getId()}).");

            $infoResponse = $this->b2bApi->getOrderInfo($guid);

            if ($infoResponse['http_code'] !== 200) {
                $this->log("Catch error while trying to sync information about Invoice (ID: {$invoice->getId()})."
                    ." Error: {$infoResponse['data']} | Skipped it!", ['guid' => $guid]);
                continue;
            }

            $invoice->setStatusGuid($infoResponse['data']['payment_status_guid']);
            $invoice->setStatusText($infoResponse['data']['payment_status']);

            $this->em->persist($invoice);
            $this->em->flush();
        }
    }

    /**
     * Make invoices to organizations
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    private function _checkAndMakeInvoices()
    {
        $this->output->writeln("==========");

        /** @var ConferenceOrganizationRepository $conferenceOrganizationRepo */
        $conferenceOrganizationRepo = $this->em->getRepository(ConferenceOrganization::class);
        $dataToInvoices = $conferenceOrganizationRepo->findToMakeInvoice(self::CROS_YEAR);
        $this->log("Found ".count($dataToInvoices)." organizations to make invoice.");

        foreach ($dataToInvoices as $dataToInvoice) {
            $this->log("Check Organization (ID: {$dataToInvoice['org_id']}) to make invoice.");

            if (!isset($dataToInvoice['user_guid'], $dataToInvoice['user_email'], $dataToInvoice['user_phone'])) {
                $this->log("Not enough data to make invoice for Organization (ID: {$dataToInvoice['org_id']}). Skipped it!", $dataToInvoice);
                continue;
            }

            $createResponse = $this->b2bApi->createOrder([
                'contractor_guid'   => $dataToInvoice['org_b2b_guid'],
                'user_guid'         => $dataToInvoice['user_guid'],
                'phone'             => $dataToInvoice['user_phone'],
                'email'             => $dataToInvoice['user_email'],
                'services'          => [ [ 'sku' => self::CROS_SERVICE_SKU, 'amount' => $dataToInvoice['fresh_amount'] ] ],
            ]);

            if ($createResponse['http_code'] !== 200) {
                $this->log("Catch error while trying to make invoice for Organization (ID: {$dataToInvoice['org_id']})."
                    ." Error: {$createResponse['data']} | Skipped it! brrrrra", $dataToInvoice);
                continue;
            }

            try {
                /** @var ConferenceOrganization $conferenceOrganizationReference */
                $conferenceOrganizationReference = $this->em->getReference(ConferenceOrganization::class, $dataToInvoice['conf_org_id']);
            } catch (\Exception $e) {
                $this->log("Catch error while trying to get reference "
                    ."ConferenceOrganization (ID: {$dataToInvoice['conf_org_id']}) of Organization (ID: {$dataToInvoice['org_id']})."
                    ." Error: {$e->getMessage()} | Skipped it!", $dataToInvoice);
                continue;
            }

            $invoice = new Invoice();
            $invoice->setConferenceOrganization($conferenceOrganizationReference);
            $invoice->setAmount($dataToInvoice['fresh_amount']);
            $invoice->setOrderGuid($createResponse['data']['guid']);
            $invoice->setStatusGuid($createResponse['data']['payment_status_guid']);
            $invoice->setStatusText($createResponse['data']['payment_status']);

            $this->em->persist($invoice);
            $this->em->flush();
        }

    }

    protected function log($msg, array $data = [])
    {
        $this->output->writeln($msg);
        $this->logger->notice("[b2b-sync] $msg", $data);
    }
}