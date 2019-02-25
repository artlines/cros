<?php

namespace App\Command;

use App\Entity\Participating\Organization;
use App\Service\B2BApi;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncWithB2B extends Command
{
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
        $this->_syncContractors();


        // sync users
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
        /** @var Organization[] $organizations */
        $organizations = $this->em->getRepository(Organization::class)->findBy([
            'b2b_guid' => null,
        ]);
        $this->log("Found ".count($organizations)." without b2b_guid which will be synchronized.");

        foreach ($organizations as $organization) {
            $response = $this->b2bApi->findContractorByInnKpp($organization->getInn(), $organization->getKpp());

            if (!isset($response['error'])) {
                $b2bGuid = $response['fixed_guid'];
                $organization->setB2bGuid($b2bGuid);
                $this->log('Update organization (ID: '.$organization->getId().'). Set b2b_guid `'.$b2bGuid.'`.');
            } else {
                // TODO:
            }
        }
    }

    protected function log($msg, array $data = [])
    {
        $this->logger->notice("[b2b-sync] $msg", $data);
    }
}