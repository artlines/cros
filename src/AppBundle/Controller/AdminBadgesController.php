<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Conference;
use AppBundle\Entity\Organization;
use AppBundle\Entity\User;
use AppBundle\Repository\OrganizationRepository;
use AppBundle\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \ZipArchive;

class AdminBadgesController extends Controller
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
    public function adminBadgesAction()
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
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
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
            $org->addUser($user);
            $orgs[] = $org;
            $badge_type = $request->get('type');
        }
        else{
            $man = false;
            /** @var Conference $conf */
            $conf = $this->getDoctrine()
                ->getRepository('AppBundle:Conference')
                ->findOneBy(array('year' => $year));

            /** @var OrganizationRepository $orgRepository */
            $orgRepository = $this->getDoctrine()->getRepository('AppBundle:Organization');

            $orgs = $orgRepository->findAllByConferenceWoNot($conf->getId(), false);
        }

        $this->w = 3508;                    // Ширина шаблона
        $this->h = 2482;                    // Высота шаблона
        $this->c = $this->w / 2;            // Центр шаблона

        $this->pt = 950;                    // Отступ сверху
        $this->pb = 100;                    // Отступ снизу
        $this->l1 = 585;                    // Отступ слева 1
        $this->l2 = $this->c + $this->l1;   // Отступ слева 2

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
            if($badge_type){
                $temp = $badge_type;
                if($temp == 'nag'){
                    $this->pt = 1060;
                    $this->l1 = 680;
                    $this->l2 = $this->c + $this->l1 - 515;
                }
                else{
                    $this->pt = 950;                            // Отступ сверху
                    $this->pb = 100;                            // Отступ снизу
                    $this->l1 = 585;                            // Отступ слева 1
                    $this->l2 = $this->c + $this->l1 - 335;     // Отступ слева 2
                }
            }
            else {
                $temp = 'all';
                if ($org->getId() == 1) {
                    $temp = 'nag';
                } elseif ($org->getSponsor() == 1 || $org->getStatus() == 3) {
                    $temp = 'vip';
                }
                if ($temp == 'nag') {
                    continue;
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
                if($temp == 'nag'){
                    $font_size = 110;
                }
                else {
                    $font_size = 100;
                }
                $fio = str_replace('>', '', str_replace('<', '', str_replace('\"', '', $user->getLastName() . ' ' . $user->getFirstName() . ' ' . $user->getMiddleName())));

                $this->addText($fio, $font_size, $font_color);

                if ($this->temp != 'nag') {
                    // Должность участника
                    $post_relative_top = $this->post_top;
                    if ($post_relative_top > 450) { //450 - 3 string
                        $post_relative_top = $post_relative_top - ($post_relative_top / 12);
                    }
                    elseif ($post_relative_top > 250){ //250 - 2 string
                        $post_relative_top = $post_relative_top - ($post_relative_top / 6);
                    }
                    $font_size = 60;
                    $font_color = imagecolorallocate($this->badge, 43, 42, 41);
                    $post = str_replace('>', '', str_replace('<', '', str_replace('\"', '', $user->getPost())));
                    mb_internal_encoding('UTF-8');
                    $post = mb_convert_case(mb_substr($post, 0, 1), MB_CASE_TITLE) . mb_substr($post, 1);

                    $top_minus = -800;
                    //$top_plus_minus = $top_minus + $post_relative_top;
                    $top_plus_minus = -350;

                    $this->midorg($post, $font_size, $font_color, $this->l1, false, $top_plus_minus);
                    $this->midorg($post, $font_size, $font_color, $this->l2, false, $top_plus_minus);
                }

                // Организация
                if($temp == 'nag'){
                    $font_size = 100;
                }
                else {
                    $font_size = 90;
                }
                if ($temp != 'nag') {
                    $font_size = 60;
                }

                $font_color = imagecolorallocate($this->badge, 57, 56, 56);
                $org_name = strtoupper(
                    str_replace(array('ООО ', 'ЗАО ', 'ОАО ', 'НОУ ', 'ПАО ', 'АО ', 'ТОО ', 'РУП', 'AО ', '"', "'", "»", "«", '>', '<', '\"'), '', str_replace('ООО"ЭйрЛинк"', 'ЭйрЛинк', $org->getName())));
                $this->midorg($org_name, $font_size, $font_color, $this->l1, true);
                $this->midorg($org_name, $font_size, $font_color, $this->l2, true);

                // Город
                if($temp == 'nag'){
                    $font_size = 50;
                }
                else {
                    $font_size = 35;
                }
                $town = str_replace("»", '"', str_replace("«", '"', str_replace('>', '"', str_replace('<', '"', str_replace('\"', '"', $org->getCity())))));
                if ($org->getId() == 1) {
                    if ($user->getId() == 87) {
                        $town = 'г. Новосибирск';
                    }
                    if ($user->getId() == 88) {
                        $town = 'г. Москва';
                    }
                }
                $this->midorg($town, $font_size, $font_color, $this->l1, false, 100);
                $this->midorg($town, $font_size, $font_color, $this->l2, false, 100);


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
                    $message = 'http://cros.nag.ru/uploads/badges/personal/'.$t.'.png';
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
            $textbox = imagettfbbox($font_size, 0, $this->root . '/templates/arial.ttf', $result);
            $minY = min(array($textbox[1], $textbox[3], $textbox[5], $textbox[7]));
            $maxY = max(array($textbox[1], $textbox[3], $textbox[5], $textbox[7]));
            $this->post_top = ($maxY - $minY);
        } else {
            foreach ($words as $word) {
                $tmp_str = $result . ' ' . $word;

                $textbox = imagettfbbox($font_size, 0, $this->root . '/templates/arial.ttf', $tmp_str);

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
     * @param string        $text
     * @param integer       $font_size
     * @param string|bool   $font_color
     */
    public function addText($text, $font_size = 90, $font_color = false){
        if(!$font_color){
            imagecolorallocate($this->badge, 57, 56, 56);
        }
        imagettftext(
            $this->badge,
            $font_size,
            0,
            $this->l1,
            $this->pt,
            $font_color,
            $this->root . 'templates/arial.ttf',
            $this->wraper($text, $font_size, true)
        );
        imagettftext(
            $this->badge,
            $font_size,
            0,
            $this->l2,
            $this->pt,
            $font_color,
            $this->root . 'templates/arial.ttf',
            $this->wraper($text, $font_size, true)
        );
    }

    public function midorg($str, $font_size, $font_color, $left, $bold = false, $top_plus = 0)
    {
        if ($bold) {
            $font_file = $this->root . "/templates/arialbd.ttf";
        } else {
            $font_file = $this->root . "/templates/arial.ttf";
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
            //if($this->temp != "nag"){
                $mid_left = 425;
            //}
            //else{
            //    $mid_left = 0;
            //}
            $left_str = $left - $mid_left + round((($this->w / 2) - ($textbox[2] - $textbox[0])) / 2);
            imagettftext(
                $this->badge,
                $font_size,
                0, // rotate
                $left_str, // left
                /*1425*/ 1750 + $t_h + $top_plus, // top
                $font_color,
                $font_file,
                $string
            );
            $t_h = $t_h + $font_size + ($font_size / 2 - 5);
        }
    }
}
