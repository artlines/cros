<?php

namespace App\Command;

use App\Entity\Conference;
use Doctrine\ORM\EntityManagerInterface;
use samdark\sitemap\Sitemap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SitemapGenerateCommand extends Command
{
    /** @var EntityManagerInterface */
    protected $em;

    /**
     * SitemapGenerateCommand constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    public function configure()
    {
        $this
            ->setName('app:sitemap:generate')
            ->setDescription('Generate sitemap.xml file in root directory');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $siteName = 'https://cros.nag.ru/';
        $sitemapPath = __DIR__ . '/../../public/sitemap.xml';

        $sitemap = new Sitemap($sitemapPath);

        $sitemap->addItem($siteName.'registration');
        $sitemap->addItem($siteName.'faq');
        $sitemap->addItem($siteName.'privacy');
        $sitemap->addItem($siteName.'speakers');
        $sitemap->addItem($siteName.'program');
        $sitemap->addItem($siteName.'price');

        $sitemap->addItem($siteName.'info/targets');
        $sitemap->addItem($siteName.'info/organize');
        $sitemap->addItem($siteName.'info/sponsors');

        $sitemap->addItem($siteName.'archive');
        /** @var Conference $conference */
        foreach ($this->em->getRepository(Conference::class)->findAll() as $conference) {
            $sitemap->addItem($siteName.'archive/'.$conference->getYear());
        }

        $sitemap->write();

        $output->writeln('Done.');
    }
}