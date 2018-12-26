<?php

namespace App\Controller\Admin;

use AppBundle\Entity\Conference;
use AppBundle\Entity\Organization;
use AppBundle\Entity\User;
use AppBundle\Repository\OrganizationRepository;
use AppBundle\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use \ZipArchive;

class BadgesController extends AbstractController
{
    protected $root = '';
    protected $post_top;
    protected $badge;
    protected $w;
    protected $h;
    protected $c;
    protected $pt;
    protected $pb;
    protected $l1;
    protected $l2;
    protected $temp;

    /**
     * Список зарегистрированных пользователей
     *
     * @Route("/admin/badges", name="admin-badges")
     *
     * @return object
     */
    public function adminBadges()
    {
        $archive = false;
        $gen_date = false;
        $year = date('Y');
        $filename = realpath($this->container->getParameter('kernel.root_dir') . '/../web/uploads/badges/' . $year . '.zip');

        if (file_exists($filename)) {
            $archive = $year;
            $gen_date = date('d F Y H:i:s', filemtime($filename));
        }

        return $this->render('admin/members/badges.html.twig', array(
            'archive' => $archive,
            'gen_date' => $gen_date,
        ));
    }

    /**
     * @Route("/admin/badges/find", name="admin-badges-find")
     * @param Request $request
     * @return object
     */
    public function adminBadgesFind(Request $request)
    {
        $search_str = $request->get('search');

        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository('App:User');
        /** @var User $users */
        $users = $userRepository->search($search_str);

        return $this->render('admin/members/find.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * @Route("/admin/badges/generate", name="admin-generate-badges")
     *
     * @param Request $request
     * @return object
     */
    public function adminBadgesGenerate(Request $request)
    {
        $this->root = realpath($this->container->getParameter('kernel.root_dir') . '/../web/uploads/badges/') . '/';
        $year = date('Y');
        $badge_type = false;

        $man = $request->get('man');
        if($man != null){
            $orgs = array();
            $org = new Organization();
            $org->setName($request->get('organization'));
            $org->setCity($request->get('city'));
            $user = new User();
            $user->setLastName($request->get('last_name'));
            $user->setFirstName($request->get('first_name'));
            $user->setMiddleName($request->get('middle_name'));
            $user->setPost($request->get('post'));
            $user->setNickname($request->get('nickname'));
            $org->addUser($user);
            $orgs[] = $org;
            $badge_type = $request->get('type');
        }
        else{
            $man = false;
            /** @var Conference $conf */
            $conf = $this->getDoctrine()
                ->getRepository('App:Conference')
                ->findOneBy(array('year' => $year));

            /** @var OrganizationRepository $orgRepository */
            $orgRepository = $this->getDoctrine()->getRepository('App:Organization');

            $orgs = $orgRepository->findAllByConferenceWoNot($conf->getId(), false);
        }

        $message = false;
        $is_generated = false;
        $root = $this->root;

        if (file_exists($root . $year . '.zip')) {
            unlink($root . $year . '.zip');
        }

        if(!$man) {
            $zip = new ZipArchive();
            $zip->open($root . $year . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);
        }

        set_time_limit(count($orgs) * 10);

        /** @var Organization $org */
        foreach ($orgs as $org) {
            if ($badge_type) {
                $temp = $badge_type;
                $this->w = 2598;                    // Ширина шаблона
                $this->h = 1772;                    // Высота шаблона
                if ($badge_type == 'nag') {
                    $this->w = 3072;                    // Ширина шаблона
                    $this->h = 1890;                    // Высота шаблона
                }
                $this->c = $this->w / 2;            // Центр шаблона
                if ($temp == 'nag') {
                    $this->pt = 600;                    // Отступ сверху
                    $this->pb = 100;                    // Отступ снизу
                    $this->l1 = 165;                    // Отступ слева 1
                    $this->l2 = $this->c + $this->l1;   // Отступ слева 2
                } else {
                    $this->pt = 560;                    // Отступ сверху
                    $this->pb = 400;                    // Отступ снизу
                    $this->l1 = 175;                    // Отступ слева 1
                    $this->l2 = $this->c + $this->l1;   // Отступ слева 2
                }
            } else {
                $temp = 'all';
                if ($org->getId() == 1) {
                    $temp = 'nag';
                }
                $this->w = 2598;                    // Ширина шаблона
                $this->h = 1772;                    // Высота шаблона
                if ($temp == 'nag') {
                    $this->w = 3072;                    // Ширина шаблона
                    $this->h = 1890;                    // Высота шаблона
                }
                $this->c = $this->w / 2;            // Центр шаблона
                if ($temp == 'nag') {
                    $this->pt = 600;                    // Отступ сверху
                    $this->pb = 100;                    // Отступ снизу
                    $this->l1 = 165;                    // Отступ слева 1
                    $this->l2 = $this->c + $this->l1;   // Отступ слева 2
                } else {
                    $this->pt = 560;                    // Отступ сверху
                    $this->pb = 400;                    // Отступ снизу
                    $this->l1 = 175;                    // Отступ слева 1
                    $this->l2 = $this->c + $this->l1;   // Отступ слева 2
                }
            }

            $this->temp = $temp;
            $temp_path = $root . 'templates/' . $temp . '.png';

            $users = $org->getUsers();
            /** @var User $user */
            foreach ($users as $user) {
                $this->badge = imagecreatefrompng($temp_path);


                // Имя участника
                $font_color = imagecolorallocate($this->badge, 43, 42, 41);
                $font_size = 76;
                $fio = str_replace('>', '', str_replace('<', '', str_replace('\"', '', $user->getFirstName() . ' ' . $user->getLastName()))); //  . ' ' . $user->getMiddleName()
                $this->addText($fio, $font_size, $font_color, true);


                // Ник участника
                $font_color = imagecolorallocate($this->badge, 23, 22, 21);
                $font_size = 76;
                $_pt = 860;
                if ($temp == 'nag') {
                    $_pt = 925;
                }
                $nick = $user->getNickname();
                if ($nick !== null) {
                    $this->addText(substr($nick, 0, 16), $font_size, $font_color, false, $_pt);
                }


                // Должность участника
                $font_size = 48;
                $font_color = imagecolorallocate($this->badge, 43, 42, 41);
                mb_internal_encoding('UTF-8');
                $post = str_replace('>', '', str_replace('<', '', str_replace('\"', '', $user->getPost())));
                $post = mb_convert_case(mb_substr($post, 0, 1), MB_CASE_TITLE) . mb_substr($post, 1);
                $_pt = 970;
                if ($temp == 'nag') {
                    $_pt = 1025;
                }
                $this->addText($post, $font_size, $font_color, false, $_pt);


                // Организация
                $font_size = 100;
                $font_color = imagecolorallocate($this->badge, 57, 56, 56);
                $org_name = strtoupper(
                    str_replace(array('ООО ', 'ЗАО ', 'ОАО ', 'НОУ ', 'ПАО ', 'АО ', 'ТОО ', 'РУП', 'AО ', '"', "'", "»", "«", '>', '<', '\"'), '', str_replace('ООО"ЭйрЛинк"', 'ЭйрЛинк', $org->getName())));

                $_pt = -140;
                if ($this->temp == 'nag') {
                    $_pt = -30;
                }
                $this->midorg($org_name, $font_size, $font_color, 0, false, $_pt);
                $this->midorg($org_name, $font_size, $font_color, $this->c, false, $_pt);


                // Город
                $font_size = 100;
                $town = str_replace("»", '"', str_replace("«", '"', str_replace('>', '"', str_replace('<', '"', str_replace('\"', '"', $org->getCity())))));
                if ($org->getId() == 1) {
                    if ($user->getId() == 87) {
                        $town = 'Новосибирск';
                    }
                    if ($user->getId() == 88) {
                        $town = 'Москва';
                    }
                }
                $_pt = 90;
                if ($this->temp == 'nag') {
                    $_pt = 190;
                }
                $this->midorg($town, $font_size, $font_color, 0, false, $_pt);
                $this->midorg($town, $font_size, $font_color, $this->c, false, $_pt);


                // Формирование имени файла, сохранение изображение в файл
                $t = str_replace('/', "", str_replace(" ", "_", str_replace('"', '', str_replace("»", '', str_replace("«", '', str_replace('>', '', str_replace('<', '', str_replace('\"', '', $org_name)))))))) . "_" . $user->getLastName() . '_' . $user->getFirstName();
                if(!$man){
                    imagepng($this->badge, $root . 'forgen/' . $t . '.png');
                }
                else{
                    imagepng($this->badge, $root . 'personal/' . $t . '.png');
                }
                imagedestroy($this->badge);
                if(!$man) {
                    $zip->addFile($root . 'forgen/' . $t . '.png', $t . '.png');
                }
                else{
                    $message = 'https://'.$request->getHttpHost().'/uploads/badges/personal/'.$t.'.png';
                    return new Response($message);
                }
                $message = 'ok';
                $is_generated = true;
            }
        }

        if(!$man) {
            $zip->close();
        }
        $rm_images = glob($root . 'forgen/*');
        foreach ($rm_images as $rm_image) {
            if (is_file($rm_image)) {
                unlink($rm_image);
            }
        }
        if (!$is_generated) {
            $message = 'nousers';
        }

        return new Response($message);
    }

    public function wraper($str, $font_size, $spacebr = false)
    {
        $result = '';
        $words = explode(' ', $str);
        if ($spacebr) {
            $result = str_replace(" ", "\n", $str);
            $textbox = imagettfbbox($font_size, 0, $this->root . '/templates/helveticaneue.ttf', $result);
            $minY = min(array($textbox[1], $textbox[3], $textbox[5], $textbox[7]));
            $maxY = max(array($textbox[1], $textbox[3], $textbox[5], $textbox[7]));
            $this->post_top = ($maxY - $minY);
        } else {
            foreach ($words as $word) {
                $tmp_str = $result . ' ' . $word;

                $textbox = imagettfbbox($font_size, 0, $this->root . '/templates/helveticaneue.ttf', $tmp_str);

                if ($textbox[2] > 1169) {
                    $result .= ($result == "" ? "" : "\n") . $word;
                } else {
                    $result .= ($result == "" ? "" : " ") . $word;
                }
            }
        }
        return str_replace('>', '', str_replace('<', '', str_replace('\"', '', $result)));
    }

    /**
     * Add text to left and right page of badge
     *
     * @param string $text              String
     * @param integer $font_size        Font size
     * @param string|bool $font_color   Font color
     * @param string|null $pt           Padding top
     * @param string|null $pl           Padding left
     */
    public function addText($text, $font_size = 90, $font_color = false, $spacebr = false, $pt = null, $pl = null){
        if(!$font_color){
            imagecolorallocate($this->badge, 57, 56, 56);
        }

        $_pt = isset($pt) ? $pt : $this->pt;
        $_pl = isset($pl) ? $pl : $this->l1;

        imagettftext(
            $this->badge,
            $font_size,
            0,
            $_pl,
            $_pt,
            $font_color,
            $this->root . 'templates/helveticaneue.ttf',
            $this->wraper($text, $font_size, $spacebr)
        );
        imagettftext(
            $this->badge,
            $font_size,
            0,
            $_pl + $this->c,
            $_pt,
            $font_color,
            $this->root . 'templates/helveticaneue.ttf',
            $this->wraper($text, $font_size, $spacebr)
        );
    }


    public function midorg($str, $font_size, $font_color, $left, $bold = false, $top_plus = 0)
    {
        if ($bold) {
            $font_file = $this->root . "/templates/helveticaneuebold.ttf";
        } else {
            $font_file = $this->root . "/templates/helveticaneue.ttf";
        }
        $result = '';
        $words = explode(' ', $str);
        foreach ($words as $word) {
            $tmp_str = $result . ' ' . $word;
            $textbox = imagettfbbox($font_size, 0, $font_file, $tmp_str);

            if ($textbox[2] > 1169) {
                $result .= ($result == "" ? "" : "\n") . $word;
            } else {
                $result .= ($result == "" ? "" : " ") . $word;
            }
        }
        $strings = explode("\n", $result);
        $textbox = imagettfbbox($font_size, 0, $font_file, $result);

        // Если высота блока с текстом выше 300, делаем шрифт меньше на единицу
        if (($textbox[1] - $textbox[7]) > 300) {
            do {
                $font_size = $font_size - 1;
                $textbox = imagettfbbox($font_size, 0, $font_file, $result);
            } while (($textbox[1] - $textbox[7]) > 300);
        }
        $textbox = imagettfbbox($font_size, 0, $font_file, $result);

        $t_h = ((300 - ($textbox[1] - $textbox[7])) / 2) - 10;
        foreach ($strings as $string) {
            $textbox = imagettfbbox($font_size, 0, $font_file, $string);
            $mid_left = 0;
            if ($this->temp == 'nag') {
                $mid_left = 0;
            }
            $left_str = $left - $mid_left + round((($this->w / 2) - ($textbox[2] - $textbox[0])) / 2);
            imagettftext(
                $this->badge,
                $font_size,
                0, // rotate
                $left_str, // left
                1425 + $t_h + $top_plus, // top
                $font_color,
                $font_file,
                $string
            );
            $t_h = $t_h + $font_size + ($font_size / 2 - 5);
        }
    }
}
