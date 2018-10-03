<?php

namespace App\Controller\Trace;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

use OpenCensus\Trace\Integrations\Guzzle\Middleware;

trait FetchFromPhp {

    public function fetchPage() {
        try {
            $result = $this->getGuzzleClient()->get('http://distributed-tracing-test-php2:8000/fetch');
            return $result->getBody();
        } catch (\Exception $e) {
            die(var_dump($e));
        }

        return '[]';
    }

    /**
     * Undocumented function
     * @see https://github.com/census-instrumentation/opencensus-php/blob/master/docs/content/integrating-guzzle.md
     * @return void
     */
    public function getGuzzleClient() {
        $stack = new HandlerStack();
        $stack->setHandler(\GuzzleHttp\choose_handler());
        $stack->push(new Middleware());
        $client = new Client(['handler' => $stack]);
        return $client;
    }

}