<?php

namespace App\OpenCensus\Trace\Propagator;

use App\OpenCensus\Trace\Propagator\PythonFormater;

use OpenCensus\Trace\Propagator\PropagatorInterface;
use OpenCensus\Trace\SpanContext;

class HttpHeaderPropagator implements PropagatorInterface {

    const DEFAULT_HEADER = 'HTTP_TRACEPARENT';

    /**
     * @var FormatterInterface
     */
    private $formatter;

    /**
     * @var string
     */
    private $header;

    /**
     * @see BaseHttpHeaderPropagator::start()
     */
    public function __construct(FormatterInterface $formatter = null, $header = null)
    {
        $this->formatter = $formatter ?: new PythonFormater();
        $this->header = $header ?: self::DEFAULT_HEADER;
    }

    /**
     * Generate a SpanContext object from the all the HTTP headers
     *
     * @param array $headers
     * @return SpanContext
     */
    public function extract($headers)
    {
        if (array_key_exists($this->header, $headers)) {
            return $this->formatter->deserialize($headers[$this->header]);
        }
        return new SpanContext();
    }

    /**
     * Persists the current SpanContext back into the results of this request
     *
     * @param SpanContext $context
     * @param array $container
     * @return array
     */
    public function inject(SpanContext $context, $container)
    {
        $header = $this->key();
        $value = $this->formatter->serialize($context);
        if (!headers_sent()) {
            header("$header: $value");
        }
        return [
            $header => $value
        ];
    }

    /**
     * Returns the current formatter
     *
     * @return FormatterInterface
     */
    public function formatter()
    {
        return $this->formatter;
    }

    /**
     * Return the key used to propagate the SpanContext
     *
     * @return string
     */
    public function key()
    {
        return str_replace('_', '-', preg_replace('/^HTTP_/', '', $this->header));
    }

}