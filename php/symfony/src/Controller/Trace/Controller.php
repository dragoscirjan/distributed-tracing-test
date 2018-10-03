<?php

namespace App\Controller\Trace;

use OpenCensus\Trace\Tracer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// trait Controller
class Controller extends AbstractController
{
    public function index() {
        $span = Tracer::startSpan(['name' => 'controller.index.php']);
        var_dump($span);
        $scope = Tracer::withSpan($span);

        $data = '[]';
        try {
            $data = $this->fetchPage();
        } catch (\Exception $e) {  }
        
        $result = new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
        try {
            $result = new Response($data, Response::HTTP_OK);
        } catch (\Exception $e) {  }

        $scope->close();
        return $result;
    }

    public function fetch() {
        var_dump(getallheaders());
        $span = Tracer::startSpan(['name' => 'controller.fetch.php']);
        $scope = Tracer::withSpan($span);

        try {
            $data = $this->getGuzzleClient()->get('https://jsonplaceholder.typicode.com/users')->getBody();
        } catch (\Exception $e) {
            $data = '[]';
        }

        $scope->close();
        return new Response($data, Response::HTTP_OK);
    }

    public function twig() {
        $data = [];
        try {
            $data = json_decode($this->fetchPage());
        } catch (\Exception $e) {  }

        $span = Tracer::startSpan(['name' => 'twig.generate']);
        $scope = Tracer::withSpan($span);
        $result = new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
        try {
            $result = $this->render('abstract/index.html.twig', [
                'controller_name' => get_class($this),
                'data' => $data
            ]);
        } finally {
            $scope->close();
        }

        return $result;
    }
}
