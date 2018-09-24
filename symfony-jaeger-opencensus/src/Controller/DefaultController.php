<?php

namespace App\Controller;

use OpenCensus\Trace\Tracer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController {

    public function index() {

        $span = Tracer::startSpan(['name' => 'expensive-operation-1']);

        $scope = Tracer::withSpan($span);

        $result = Response('', Response::HTTP_INTERNAL_SERVER_ERROR);

        try {
            $result = $this->render('abstract/index.html.twig', [
                'controller_name' => get_class($this)
            ]);
        } finally {
            var_dump($scope);
            $scope->close();
        }

        return $result;
    }

    public function fetch() {

        $span = Tracer::startSpan(['name' => 'expensive-operation-1']);

        $scope = Tracer::withSpan($span);
        try {
            usleep(5000);
        } finally {
            $scope->close();
        }

        return new Response(json_encode([
            'message' => 'This is a test message!'
        ]));
    }

}