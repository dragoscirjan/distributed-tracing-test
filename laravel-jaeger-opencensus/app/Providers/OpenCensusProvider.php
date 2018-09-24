<?php
/**
 * Created by PhpStorm.
 * User: dragos.cirjan
 * Date: 24/09/2018
 * Time: 12:38
 */

namespace App\Providers;

use Illuminate\Log\Logger;
use Illuminate\Support\ServiceProvider;
use OpenCensus\Trace\Exporter\LoggerExporter;
use OpenCensus\Trace\Tracer;
use OpenCensus\Trace\Integrations\Laravel;
use OpenCensus\Trace\Integrations\Mysql;
use OpenCensus\Trace\Integrations\PDO;

class OpenCensusProvider extends ServiceProvider
{
    public function boot()
    {
        if (php_sapi_name() == 'cli') {
            return;
        }

        // Enable OpenCensus extension integrations
        Laravel::load();
        Mysql::load();
        PDO::load();

        // Start the request tracing for this request
        $exporter = new LoggerExporter(new Logger(new \Monolog\Logger('traces')));
        Tracer::start($exporter);

        // Create a span that starts from when Laravel first boots (public/index.php)
//        Tracer::inSpan(['name' => 'bootstrap', 'startTime' => LARAVEL_START], function () {});
    }
}
