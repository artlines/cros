<?php

namespace AppBundle\Command;

use AppBundle\Manager\TgChatManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TgNotifyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:tg-notify')
            ->setDescription('Search lectures which will start at 15 minutes AND notify telegram chats');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var TgChatManager $tgChatManager */
        $tgChatManager = $this->getContainer()->get('tg.chat.manager');

        $current_date_in_sochi = (new \DateTime())->setTimezone(new \DateTimeZone('Europe/Moscow'));
        $output->writeln("In Sochi now ".$current_date_in_sochi->format("H:i d.m.Y"));

        $res = $tgChatManager->checkAndNotifySubscribes();

        $output->writeln("Done.");
    }

}