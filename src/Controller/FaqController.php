<?php

namespace App\Controller;

use App\Old\Entity\AppendText;
use App\Old\Entity\Faq;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FaqController extends AbstractController
{
    /**
     * @Route("/faq", name="faq")
     */
    public function faq()
    {
        /** @var Faq $faq */
        $faq = $this->getDoctrine()->getRepository('App:Faq')->findBy(['isActive' => true]);

        /** @var AppendText $append_text */
        $append_text = $this->getDoctrine()->getRepository('App:AppendText')->findOneBy(['alias' => 'faq']);

        return $this->render('frontend/faq/show.html.twig', [
            'faq'           => $faq,
            'append_text'   => $append_text,
        ]);
    }
}
