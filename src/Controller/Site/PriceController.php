<?php

namespace App\Controller\Site;

use App\Entity\Abode\RoomType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PriceController extends AbstractController
{
    /**
     * @Route("/price", name="price")
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function price(EntityManagerInterface $em)
    {
        $roomTypes = $em->getRepository(RoomType::class)->findBy([], ['title' => 'ASC']);

        return $this->render('site/price.html.twig', [
            'roomTypes' => $roomTypes
        ]);
    }
}