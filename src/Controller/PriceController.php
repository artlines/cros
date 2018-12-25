<?php

namespace App\Controller;

use App\Old\Entity\AppendText;
use App\Old\Entity\Apartament;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PriceController extends AbstractController
{
    /**
     * @Route("/price", name="price")
     */
    public function price(EntityManagerInterface $entityManager)
    {
        $conf = $entityManager->getRepository('App\Old\Entity\Conference')
            ->findOneBy(['year' => date('Y')]);

        /** @var Apartament $apartaments */
        $apartaments = $entityManager->getRepository('App\Old\Entity\Apartament')
            ->findBy(['conferenceId' => $conf->getId()]);

        /** @var AppendText $append_text */
        $append_text = $entityManager->getRepository('App\Old\Entity\AppendText')
            ->findOneBy(['alias' => 'price']);

        return $this->render('frontend/price/show.html.twig', [
            'prices' => $apartaments,
            'append_text' => $append_text,
        ]);
    }
}
