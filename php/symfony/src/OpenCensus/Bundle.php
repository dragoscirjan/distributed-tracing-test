<?php

namespace App\OpenCensus;

use Monolog\Logger;

use Ramsey\Uuid\Uuid;

use App\OpenCensus\Trace\Exporter\JaegerExporter;

use OpenCensus\Trace\Integrations\Doctrine;
use OpenCensus\Trace\Integrations\Symfony;
use OpenCensus\Trace\Propagator\HttpHeaderPropagator;
use OpenCensus\Trace\Tracer;

use Symfony\Component\HttpKernel\Bundle\Bundle as BaseBundle;

class Bundle extends BaseBundle
{
    public function boot()
    {
        $this->setupOpenCensus();
    }

    public function tracerStart() {
        // Start the request tracing for this request
        $exporter = new JaegerExporter('my-jaeger', [
            'host' => 'distributed-tracing-test-jaeger'
        ]);
        Tracer::start($exporter);
        var_dump(Tracer::spanContext());
    }

    private function setupOpenCensus()
    {
        if (php_sapi_name() == 'cli') {
            return;
        }

        $this->tracerStart();

        // Enable OpenCensus extension integrations
        Doctrine::load();
        Symfony::load();

    }
}