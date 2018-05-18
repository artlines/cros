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
        $output->writeln("[КРОС-2.0-18] Оповещение чатов Telegram о скором старте лекций");
        $output->writeln("");

        /** @var TgChatManager $tgChatManager */
        $tgChatManager = $this->getContainer()->get('tg.chat.manager');

        $current_date_in_sochi = (new \DateTime())->setTimezone(new \DateTimeZone('Europe/Moscow'));
        $output->writeln("В Сочи сейчас ".$current_date_in_sochi->format("H:i d.m.Y"));

        $res_arr = $tgChatManager->checkAndNotifySubscribes();

        if ($res_arr[0] !== 0) {
            $output->writeln("");
            $output->writeln("Было оповещено {$res_arr[1]} чатов о скором старте {$res_arr[0]} лекции(й)");
        } else {
            $output->writeln("");
            $output->writeln("Оповещения не производились");
        }
    }

}