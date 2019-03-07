<?php

namespace App\Command;

use App\Entity\Participating\Invoice;
use App\Repository\InvoiceRepository;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CheckForFailedAutoInvoicing extends Command
{
    const INTERVAL_VALUE__CHECK_CREATED_AT = 'PT30M';

    /** @var EntityManagerInterface */
    protected $em;

    /** @var LoggerInterface */
    protected $logger;

    /** @var Mailer */
    protected $mailer;

    /**
     * Emails which will be notified about failed auto invoicing
     * @var array|null
     */
    private $notifyEmails;

    public function __construct(
        string $name = null,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        Mailer $mailer,
        ParameterBagInterface $parameterBag
    ) {
        parent::__construct($name);

        $this->em       = $em;
        $this->logger   = $logger;
        $this->mailer   = $mailer;

        $this->notifyEmails = $parameterBag->has('failed_auto_invoice_emails') ? $parameterBag->get('failed_auto_invoice_emails') : null;
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
        $this->mailer->setTemplateAlias('cros2019.common');
        $params = ['header' => '', 'text' => ''];

        $sendCc = $this->notifyEmails;
        $sendTo = array_shift($sendCc);

        /** @var InvoiceRepository $invoiceRepo */
        $invoiceRepo = $this->em->getRepository(Invoice::class);

        try {
            $invoicesData = $invoiceRepo->findFailedAutoInvoicing(new \DateInterval(self::INTERVAL_VALUE__CHECK_CREATED_AT));
        } catch (\Exception $e) {
            $this->logger->notice("[failed-auto-invoicing] Catch error while create \DateInterval with value ".self::INTERVAL_VALUE__CHECK_CREATED_AT);
            exit(1);
        }

        foreach ($invoicesData as $invoiceData) {
            $this->logger->notice("[failed-auto-invoicing] Invoice (ID: {$invoiceData['id']}) didn't make in auto mode.", $invoiceData);

            if ($allowNotify && $this->notifyEmails) {
                $params['header'] .= $invoiceData['name'];
                $params['text'] .= print_r($invoiceData, true);

                $title = $invoiceData['name'].": не удалось автоматически выставить счет";

                $this->mailer->send($title, $params, $sendTo, $sendCc, null, 'error@cros.nag.ru');
            }
        };
    }
}