<?php

namespace App\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

use OpenCensus\Trace\Integrations\Guzzle\Middleware;
use OpenCensus\Trace\Tracer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController {

    public function index() {

        $data = [];
        try {
            $data = $this->fetchPage();
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

    public function fetch() {
        try {
            return $this->getGuzzleClient()->get('https://jsonplaceholder.typicode.com/users')->getBody();
        } catch (\Exception $e) {
            return '[]';
        } 
    }

    /**
     * Undocumented function
     * @see https://github.com/census-instrumentation/opencensus-php/blob/master/docs/content/integrating-guzzle.md
     * @return void
     */
    private function getGuzzleClient() {
        $stack = new HandlerStack();
        $stack->setHandler(\GuzzleHttp\choose_handler());
        $stack->push(new Middleware());
        $client = new Client(['handler' => $stack]);
        return $client;
    }

    private function fetchPage() {
        // try {
        //     return json_decode($this->getGuzzleClient()->get('http://distributed-tracing-test-py:8099')->getBody());
        // } catch (\Exception $e) {  }

        try {
            return json_decode($this->getGuzzleClient()->get($this->generateUrl('fetch'))->getBody());
        } catch (\Exception $e) {  }

        return [];
    }

}