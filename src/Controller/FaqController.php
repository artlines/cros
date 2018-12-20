<?php

namespace App\Controller;

use AppBundle\Entity\Apartament;
use AppBundle\Entity\ApartamentId;
use AppBundle\Entity\Conference;
use AppBundle\Entity\Info;
use AppBundle\Entity\Logs;
use AppBundle\Entity\Organization;
use AppBundle\Entity\Organizations;
use AppBundle\Entity\OrgToConf;
use AppBundle\Entity\User;
use AppBundle\Entity\UserToApartament;
use AppBundle\Entity\UserToConf;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class FaqController extends AbstractController
{

    /**
     * client
     */
    private $client = null;

    public function setUp(){
        $this->client = static::createClient();
    }

    /**
     * @Route("/faq", name="faq")
     */
    public function faq()
    {
        /** @var Faq $faq */
        $faq = $this->getDoctrine()
            ->getRepository('App:Faq')
            ->findBy(array('isActive' => true));

        /** @var AppendText $append_text */
        $append_text = $this->getDoctrine()
            ->getRepository('App:AppendText')
            ->findOneBy(array('alias' => 'faq'));

        return $this->render('frontend/faq/show.html.twig', array(

            'faq' => $faq,
            'append_text' => $append_text,
        ));
    }
}
