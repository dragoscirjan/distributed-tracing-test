import cherrypy
import logging
import random

from opencensus.trace import config_integration
from opencensus.trace.exporters.jaeger_exporter import JaegerExporter
from tracer.opencensus.trace.tracers.context_tracer import ContextTracer
from tracer.opencensus.trace.propagation.trace_context_http_header_format import TraceContextPropagator
from pprint import pformat

class TracerController(object):
    def __init__(self):
        logging.basicConfig(level=logging.DEBUG, format='%(asctime)s - %(levelname)s - %(message)s')
        self.tracerInstance = None
        self.propagatorInstance = None

    def getPropagator(self):
        if self.propagatorInstance == None:
            # config_integration.trace_integrations(['httplib'])
            self.propagatorInstance = TraceContextPropagator()
        logging.debug('>>>>> Propagator: %s' % pformat(self.propagatorInstance))
        return self.propagatorInstance

    def getTracer(self, new = False):
        logging.debug('>>>>>> Trace Header: %s' % pformat(cherrypy.request.headers))
        if self.tracerInstance == None or new != False:
            exporter = JaegerExporter(
                agent_host_name='distributed-tracing-test-jaeger',
                service_name='my-jaeger'
            )

            """
            @see https://github.com/census-instrumentation/opencensus-python/blob/master/opencensus/trace/propagation/trace_context_http_header_format.py#L83
            """
            span_context = self.getPropagator().from_headers(cherrypy.request.headers)
            logging.debug('>>>>> SpanContext: %s' % pformat(span_context))
            """
            @see https://github.com/census-instrumentation/opencensus-python/blob/master/opencensus/trace/tracers/context_tracer.py#L34
            """

            self.tracerInstance = ContextTracer(exporter=exporter, span_context=span_context)

        logging.debug('>>>>> Tracer: %s' % pformat(self.tracerInstance))
        return self.tracerInstance