<?php

namespace App\Controller;

use App\Controller\Trace\Controller as TraceController;
use App\Controller\Trace\FetchFromPhp;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// class DefaultController extends AbstractController {
class DefaultController extends TraceController {

    // use TraceController;
    use FetchFromPhp;

}