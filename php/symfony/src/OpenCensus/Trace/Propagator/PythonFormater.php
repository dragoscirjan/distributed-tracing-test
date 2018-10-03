<?php

namespace App\OpenCensus\Trace\Propagator;

use OpenCensus\Trace\Propagator\FormatterInterface;
use OpenCensus\Trace\SpanContext;

class PythonFormater implements FormatterInterface
{
    const CONTEXT_HEADER_FORMAT = '/00-(\w+)-(\w+)-0(\d)/';

    /**
     * Generate a SpanContext object from the Trace Context header
     *
     * @param string $header
     * @return SpanContext
     */
    public function deserialize($header)
    {
        if (preg_match(self::CONTEXT_HEADER_FORMAT, $header, $matches)) {
            return new SpanContext(
                strtolower($matches[1]),
                array_key_exists(2, $matches) && !empty($matches[2]) ? $matches[2] : null,
                array_key_exists(3, $matches) && $matches[3] === '01' ? $matches[3] == '1' : null,
                true
            );
        }
        return new SpanContext();
    }

    /**
     * Convert a SpanContext to header string
     *
     * @param SpanContext $context
     * @return string
     */
    public function serialize(SpanContext $context)
    {
        $ret = sprintf(
            '00-%s-%s-0%d',
            $context->traceId(),
            $context->spanId(),
            $context->enabled() ? '1' : '0'
        );
        return $ret;
    }
}