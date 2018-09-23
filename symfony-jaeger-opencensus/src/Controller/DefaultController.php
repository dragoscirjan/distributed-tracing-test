<?php

namespace App\Controller;

use App\Entity\Product;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController {

    public function index() {

        $faker = \Faker\Factory::create();

        $entityManager = $this->getDoctrine()->getManager();
        $product = new Product();
        $product->setName($faker->name);
        $product->setPrice($faker->randomNumber(2));
        $entityManager->persist($product);
        $entityManager->flush();

        die(var_dump($entityManager->find('\App\Entity\Product', $product->getId())));
    }

}