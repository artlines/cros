<?php
// src/AppBundle/Command/CreateUserCommand.php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use AppBundle\Entity\User;

class CreateUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // имя команды (часть после "bin/console")
            ->setName('app:create-user')
            // краткое описание, отображающееся при запуске "php bin/console list"
            ->setDescription('Меняет пароли у пользователей')
            // полное описание команды, отображающееся при запуске команды
            // с опцией "--help"
            ->setHelp('Меняет пароли у пользователей из csv файла');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
       // $this->sendEmail(); die();
        $account = array();
        $i = 0;
        if (($handle = fopen("/home/cros/www/src/AppBundle/Command/Account.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle)) !== FALSE) {
                $ex = explode(" ", $data[0]);
                $account[$i]['LastName']=$ex[0];
                $account[$i]['FirstName']=$ex[1];
                $account[$i]['email']=$data[1];
                $i++;
            }
        }
        echo "Прочитано из csv: ".$i."строк \n";
        $doctrine = $this->getContainer()->get('doctrine');
        fclose($handle);

        $UserRepository = $doctrine->getRepository('AppBundle:User');
        foreach ($account as $key => $value){
            $userdb = $UserRepository->findOneBy(array('email' => $value['email']));

            if(is_null($userdb)){
                $password = $this->generate_password(15);
                $encoder = $this->getContainer()->get('security.password_encoder');
                echo "Пользователь \"".$value['FirstName']." ".$value['LastName']."\" нет в базе создаем его, пароль будет: \t'".$password."'\n";
                $org = $doctrine
                    ->getRepository('AppBundle:Organization')
                    ->findOneBy(array('id' => 1));
                $user = new User();
                $user->setOrganizationId(1);
                $user->setOrganization($org);
                $user->setEmail($value['email']);
                $user->setFirstName($value['FirstName']);
                $user->setLastName($value['LastName']);
                $user->setUsername('+70000000'.mt_rand());
                $user->setArrival(new \DateTime('14:00 16.05.2018'));
                $user->setLeaving(new \DateTime('14:00 16.05.2018'));
                $user->setPassword($encoder->encodePassword($user, $password));
                $user->setIsActive(1);
                $user->setRoles(array('ROLE_MANAGER'));
                $em = $this->getContainer()->get('doctrine')->getEntityManager();
                $em->persist($user);
                $em->flush();
                echo "Отправим сообщение на email: ".$value['email']."\n";
                $this->sendEmail($value['FirstName']." ".$value['LastName'],$value['email'],$password,true);
            }else{
                $role = $userdb->getRoles();
                $organization = $userdb->getOrganizationId();
                if($role[0] == 'ROLE_MANAGER' && $organization == 1){
                    $encoder = $this->getContainer()->get('security.password_encoder');
                    $password = $this->generate_password(15);
                    $encoded = $encoder->encodePassword($userdb, $password);
                    $userdb->setPassword($encoded);
                    $em = $this->getContainer()->get('doctrine')->getEntityManager();
                    $em->persist($userdb);
                    $em->flush();
                    echo "Пользователю: \"".$value['FirstName']." ".$value['LastName']."\" поменяли пароль на \t'".$password."'\n";
                    echo "Отправили сообщение на почту: ".$value['email']."\n";
                    $this->sendEmail($value['FirstName']." ".$value['LastName'],$value['email'],$password,false);
                }


            }

        }

    }
    public function sendEmail($name,$login,$password,$replase)
    {
        $body;
        $title;
        if ($replase) {
            $body = "Здравствуйте, " . $name . " вам была создана учетная запись на cros nag.\nЛогин для входа: " . $login . "\nПароль: " . $password;
            $title = "Учетная запись cros nag";
        }else{
            $body = "Здравствуйте, " . $name . " ваш пароль на nag cros был изменен.\nЛогин для входа: " . $login . "\nПароль: " . $password;
            $title = "Изменение пароля на cros nag";
        }
        $message = \Swift_Message::newInstance()
            ->setFrom('cros@nag.ru')
            ->setTo($login)
            ->setSubject($title)
            ->setBody($body);
        $transport = $this->getContainer()->get('swiftmailer.transport.real');
        $mailer = \Swift_Mailer::newInstance($transport);
        $mailer->send($message);

    }
    public function generate_password($number)

    {
        $arr = array('a','b','c','d','e','f',

            'g','h','i','j','k','l',

            'm','n','o','p','r','s',

            't','u','v','x','y','z',

            'A','B','C','D','E','F',

            'G','H','I','J','K','L',

            'M','N','O','P','R','S',

            'T','U','V','X','Y','Z',

            '1','2','3','4','5','6',

            '7','8','9','0');
        $pass = "";
        for($i = 0; $i < $number; $i++)
        {
            $index = mt_rand(0, count($arr) - 1);
            $pass .= $arr[$index];
        }
        return $pass;
    }
}
