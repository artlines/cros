<?php

namespace AppBundle\Command;

use AppBundle\Service\Sms;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('app:test');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine');
        $orgRepo = $em->getRepository('AppBundle:Organization');

        $orgs = $orgRepo->findByIdsOrganizationApproved();

        var_dump($orgs);
    }
}