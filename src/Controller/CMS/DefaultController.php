<?php

namespace App\Controller\CMS;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package App\Controller\CMS
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/cms", name="cms-index")
     */
    public function index()
    {


        return $this->render('cms/index.html.twig');
    }
}