<?php

namespace AppBundle\Command;

use AppBundle\Service\Sms;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SmsTestCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('app:sms:test');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Sms $sms */
        $sms = $this->getContainer()->get('sms.service');

        $sms->setEntityClass('tg_connection');
        $sms->setEntityId(123);

        $sms->addMessage('cros_test_3', '79221544365', 'PRIVET1');
        $sms->addMessage('cros_test_4', '79221123365', 'PRIVET2');
        $sms->addMessage('cros_test_5', '79221321365', 'PRIVET3');
        $sms->addMessage('cros_test_6', '79221789365', 'PRIVET4');

        $res = $sms->send();

        var_dump($res);
    }


}