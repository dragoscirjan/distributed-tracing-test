<?php

namespace App\OpenCensus;

use App\OpenCensus\Bundle;
use App\OpenCensus\Trace\Propagator\HttpHeaderPropagator;

use App\OpenCensus\Trace\Exporter\Int64sJaegerExporter;
use OpenCensus\Trace\Tracer;

class PyBundle extends Bundle
{
    public function tracerStart() {
        // Start the request tracing for this request
        $exporter = new Int64sJaegerExporter('my-jaeger', [
            'host' => 'distributed-tracing-test-jaeger'
        ]);
        Tracer::start($exporter, [
            'propagator' => new HttpHeaderPropagator()
        ]);
        var_dump(Tracer::spanContext());
    }
}