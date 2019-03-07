<?php

namespace App\Command;

use App\Entity\Participating\Invoice;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckForFailedAutoInvoicing extends Command
{
    /** @var EntityManagerInterface */
    protected $em;

    public function __construct(string $name = null, EntityManagerInterface $em)
    {
        parent::__construct($name);

        $this->em = $em;
    }

    public function configure()
    {
        $this
            ->setName('app:check-failed-auto-invoicing')
            ->setDescription("Check for invoices which didn't make in auto mode.")
            ->addOption(
                'with-notify',
                null,
                InputOption::VALUE_NONE,
                "Send notify to email"
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $allowNotify = $input->getOption('with-notify');

        /** @var InvoiceRepository $invoiceRepo */
        $invoiceRepo = $this->em->getRepository(Invoice::class);

        $invoicesData = $inv
    }
}