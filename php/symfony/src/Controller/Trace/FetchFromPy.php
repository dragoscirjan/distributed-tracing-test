<?php

namespace App\Controller\Trace;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

use App\OpenCensus\Trace\Integrations\Guzzle\Middleware;

trait FetchFromPy {

    public function fetchPage() {
        try {
            $result = $this->getGuzzleClient()->get('http://distributed-tracing-test-py2:8099/fetch');
            return $result->getBody();
        } catch (\Exception $e) {  }

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