<?php
namespace App;

use OpenCensus\Trace\Exporter\JaegerExporter;
// use OpenCensus\Trace\Exporter\StackdriverExporter;
use OpenCensus\Trace\Integrations\Doctrine;
use OpenCensus\Trace\Integrations\Symfony;
use OpenCensus\Trace\Tracer;
use Symfony\Component\HttpKernel\Bundle\Bundle as BaseBundle;


class Bundle extends BaseBundle
{
    public function boot()
    {
        $this->setupOpenCensus();
    }

    private function setupOpenCensus()
    {
        if (php_sapi_name() == 'cli') {
            return;
        }

        // Enable OpenCensus extension integrations
        Doctrine::load();
        Symfony::load();

        // Start the request tracing for this request
        // $exporter = new StackdriverExporter();
        $exporter = new JaegerExporter([
            'host' => 'core-test-jaeger'
        ]);
        Tracer::start($exporter);
    }
}