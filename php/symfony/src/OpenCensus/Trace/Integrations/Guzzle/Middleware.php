<?php

namespace App\OpenCensus\Trace\Integrations\Guzzle;

use App\OpenCensus\Trace\Propagator\HttpHeaderPropagator;

use OpenCensus\Trace\Span;
use OpenCensus\Trace\Tracer;
use OpenCensus\Trace\Propagator\PropagatorInterface;
use Psr\Http\Message\RequestInterface;

class Middleware
{
    /**
     * @var PropagatorInterface
     */
    private $propagator;

    /**
     * Create a new Guzzle middleware that creates trace spans and propagates the current
     * trace context to the downstream request.
     *
     * @param PropagatorInterface $propagator Interface responsible for serializing trace context
     */
    public function __construct(PropagatorInterface $propagator = null)
    {
        $this->propagator = $propagator ?: new HttpHeaderPropagator();
    }

    /**
     * Magic method which makes this object callable. Guzzle middleware are expected to be
     * callables.
     *
     * @param  callable $handler The next handler in the HandlerStack
     * @return callable
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, $options) use ($handler) {
            $context = Tracer::spanContext();
            if ($context->enabled()) {
                $request = $request->withHeader(
                    $this->propagator->key(),
                    $this->propagator->formatter()->serialize($context)
                );
            }
            return Tracer::inSpan([
                'name' => 'GuzzleHttp::request',
                'attributes' => [
                    'method' => $request->getMethod(),
                    'uri' => (string)$request->getUri()
                ],
                'kind' => Span::KIND_CLIENT
            ], $handler, [$request, $options]);
        };
    }
}
