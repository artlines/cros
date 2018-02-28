<?php

namespace AppBundle\Command;

use AppBundle\Entity\Conference;
use AppBundle\Entity\ManagerGroup;
use AppBundle\Entity\Organization;
use AppBundle\Entity\OrgToConf;
use AppBundle\Entity\User;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class SendReportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('nag:send-report')
            ->setDescription('Отправить отчеты')
            ->setHelp('Отправляет менеджерам уведомления о компаниях с невыставленными и неоплаченными счетами.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Запускаем задачу',
            '================',
            '',
        ]);

        $year = date('Y');

        $output->writeln([
            'Генерируем рассылку за '.$year.' год',
            '',
        ]);

        $doctrine = $this->getContainer()->get('doctrine');

        /** @var Conference $conf */
        $conf = $doctrine
            ->getRepository('AppBundle:Conference')
            ->findOneBy(array('year' => $year));

        /** @var Organization $orgs */
        $orgs = $doctrine
            ->getRepository('AppBundle:Organization')
            ->findAllByConference($conf->getId());

        /** @var ManagerGroup $manager_groups */
        $manager_groups = $doctrine
            ->getRepository('AppBundle:ManagerGroup')
            ->findAll();

        /** @var ManagerGroup $manager_group */
        foreach ($manager_groups as $manager_group) {
            if ($manager_group->getTitle() != "гр. Самоделко") {

                $emails = array();

                $real_groups = $manager_group->getManagers();

                /** @var User $real_group */
                foreach ($real_groups as $real_group) {
                    if ($real_group->getPost() == "Группа") {
                        $emails[] = $real_group->getEmail();
                    }
                }

                $real_send = false;
                /** @var Organization $org */
                foreach ($manager_group->getManaged() as $org) {
                    if (count($org->getUsers()) != 0) {
                        /** @var OrgToConf $otc */
                        foreach ($org->getOtc() as $otc) {
                            if ($otc->getConferenceId() == $conf->getId()) {
                                if ($otc->getPaid() !== 1) {
                                    $real_send = true;
                                } elseif ($otc->getInvoice() == null) {
                                    $real_send = true;
                                }
                            }
                        }
                    }
                }

                if ($real_send) {
                    $message = \Swift_Message::newInstance()
                        ->setSubject('КРОС - счета клиентам')
                        ->setFrom('cros@nag.ru')
                        ->setTo($emails)
                        ->setBcc(array('xvanok@nag.ru', 'cros@nag.ru'))
                        ->setBody(
                            $this->getContainer()->get('templating')->render(
                                'Emails/manager_woinvoice.html.twig',
                                array(
                                    'group' => $manager_group,
                                    'conf' => $conf->getId(),
                                )
                            ),
                            'text/html'
                        );
                    $this->getContainer()->get('mailer')->send($message);
                    $output->writeln([
                        'Отправлено уведомление группе "' . $manager_group->getTitle() . '"',
                    ]);
                } else {
                    $output->writeln([
                        'У группы "' . $manager_group->getTitle() . '" нет невыставленных и неоплаченных счетов',
                    ]);
                }
            }
        }
        $output->writeln([
            '',
            'Задача успешно выполнена',
        ]);
    }
}
