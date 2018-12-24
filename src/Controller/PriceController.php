<?php

namespace App\Controller;

use App\Entity\AppendText;
use App\Entity\Apartament;
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
        $conf = $entityManager->getRepository('App\Entity\Conference')
            ->findOneBy(['year' => date('Y')]);

        /** @var Apartament $apartaments */
        $apartaments = $entityManager->getRepository('App\Entity\Apartament')
            ->findBy(['conferenceId' => $conf->getId()]);

        /** @var AppendText $append_text */
        $append_text = $entityManager->getRepository('App\Entity\AppendText')
            ->findOneBy(['alias' => 'price']);

        return $this->render('frontend/price/show.html.twig', [
            'prices' => $apartaments,
            'append_text' => $append_text,
        ]);
    }
}
