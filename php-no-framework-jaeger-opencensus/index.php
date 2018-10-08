<?php

require __DIR__.'/vendor/autoload.php';

use OpenCensus\Trace\Tracer;
use OpenCensus\Trace\Exporter\JaegerExporter;
use OpenCensus\Trace\Exporter\Jaeger\SpanConverter;
use OpenCensus\Trace\Exporter\LoggerExporter;
use OpenCensus\Trace\Exporter\ExporterInterface;
use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface {

    public function notice($message, array $context = array())
    {
        var_dump($message, $context);
//        $fh = fopen('php://stdout','w');
        $fh = fopen('php://stderr','w');
        fwrite($fh, sprintf('NOTICE: %s', $message));
        fclose($fh);
    }

    public function emergency($message, array $context = array())
    {
        // TODO: Implement emergency() method.
    }

    public function alert($message, array $context = array())
    {
        // TODO: Implement alert() method.
    }

    public function critical($message, array $context = array())
    {
        // TODO: Implement critical() method.
    }

    public function error($message, array $context = array())
    {
        // TODO: Implement error() method.
    }

    public function warning($message, array $context = array())
    {
        // TODO: Implement warning() method.
    }

    public function info($message, array $context = array())
    {
        // TODO: Implement info() method.
    }

    public function debug($message, array $context = array())
    {
        // TODO: Implement debug() method.
    }

    public function log($level, $message, array $context = array())
    {
        $this->{$level}($message, $context);
    }
}

class MyLoggerExporter implements ExporterInterface
{
    const DEFAULT_LOG_LEVEL = 'notice';

    /**
     * @var LoggerInterface The logger to write to.
     */
    private $logger;

    /**
     * @var string Logger level to report at
     */
    private $level;

    /**
     * Create a new LoggerExporter
     *
     * @param LoggerInterface $logger The logger to write to.
     * @param string $level The logger level to write as. **Defaults to** `notice`.
     */
    public function __construct(LoggerInterface $logger, $level = self::DEFAULT_LOG_LEVEL)
    {
        $this->logger = $logger;
        $this->level = $level;
    }

    /**
     * Report the provided Trace to a backend.
     *
     * @param SpanData[] $spans
     * @return bool
     */
    public function export(array $spans)
    {
        try {
            $this->logger->log($this->level, json_encode(array_map([SpanConverter::class, 'convertSpan'], $spans)));
        } catch (\Exception $e) {
            error_log('Reporting the Trace data failed: ' . $e->getMessage());
            return false;
        }
        return true;
    }
}

//$exporter = new LoggerExporter(new Logger('trace'), 'notice');
//$exporter = new MyLoggerExporter(new Logger('trace'), 'notice');
 $exporter = new JaegerExporter('my-jaeger', [
     'host' => 'distributed-tracing-test-jaeger'
 ]);

Tracer::start($exporter);

$span = Tracer::startSpan(['name' => 'expensive-operation']);

$scope = Tracer::withSpan($span);
try {
  usleep(10000);
} finally {
  $scope->close();
}

//phpinfo();