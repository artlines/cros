<?php
// src/AppBundle/Command/CreateUserCommand.php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use AppBundle\Entity\User;

class RemoveGrooupUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // имя команды (часть после "bin/console")
            ->setName('app:remove-grooup-user')
            // краткое описание, отображающееся при запуске "php bin/console list"
            ->setDescription('Меняет группы у пользователей')
            // полное описание команды, отображающееся при запуске команды
            // с опцией "--help"
            ->setHelp('Меняет группы у пользователей из csv файла');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
       // $this->sendEmail(); die();
        $account = array();
        if (($handle = fopen("/home/cros/www/src/AppBundle/Command/groop5.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $account[3][]=$data[0];
            }
        }
        if (($handle = fopen("/home/cros/www/src/AppBundle/Command/groop2.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $account[2][]=$data[0];
            }
        }
        if (($handle = fopen("/home/cros/www/src/AppBundle/Command/groop5.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $account[5][]=$data[0];
            }
        }
        if (($handle = fopen("/home/cros/www/src/AppBundle/Command/groop1.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $account[1][]=$data[0];
            }
        }
        if (($handle = fopen("/home/cros/www/src/AppBundle/Command/groop4.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $account[4][]=$data[0];
            }
        }
        foreach ($account as $key => $acc) {
            foreach ($acc as $email) {
                echo "Переместили пользователя: ".$email." в группу: ".$key."\n";
                    $userdb = $UserRepository->findOneBy(array('email' => $email));
                    if(!is_null($userdb)){
                        $userdb->setManagerGroupId($key);
                        $em = $this->getContainer()->get('doctrine')->getEntityManager();
                        $em->persist($userdb);
                        $em->flush();

                    }else{
                        echo "Пользователь ".$email." НЕ НАЙДЕН В БАЗЕ!!!\n";
                    }
            }
        }
    }
}
