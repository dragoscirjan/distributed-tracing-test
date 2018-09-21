<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AbstractController extends AbstractController
{
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
