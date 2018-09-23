<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\Routing\Annotation\Route;

class AbstractController extends BaseController
{
    // public __contruct() {}

    /**
     * @Route("/abstract", name="abstract")
     */
    public function index()
    {
        return $this->render('abstract/index.html.twig', [
            'controller_name' => 'AbstractController',
        ]);
    }
}
