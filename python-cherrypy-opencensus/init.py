from pprint import pformat
import cherrypy
import json
import requests
from opencensus.trace import config_integration
from opencensus.trace.exporters.jaeger_exporter import JaegerExporter
from opencensus.trace.propagation.trace_context_http_header_format import TraceContextPropagator, _TRACEPARENT_HEADER_NAME, _TRACE_CONTEXT_HEADER_RE
from opencensus.trace.tracers.context_tracer import ContextTracer

_TRACEPARENT_HEADER_NAME = 'PE_MATA'
_TRACE_CONTEXT_HEADER_RE = 'PE-MATA'

class Controller(object):
    def __init__(self):
        tracer = ContextTracer

        exporter = JaegerExporter(
            agent_host_name='distributed-tracing-test-jaeger',
            service_name='my-jaeger'
        )

        config_integration.trace_integrations(['httplib'])
        self.propagator = TraceContextPropagator()
        
        """
        @see https://github.com/census-instrumentation/opencensus-python/blob/master/opencensus/trace/propagation/trace_context_http_header_format.py#L83
        """
        span_context = self.propagator.from_headers(cherrypy.request.headers)
        """
        @see https://github.com/census-instrumentation/opencensus-python/blob/master/opencensus/trace/tracers/context_tracer.py#L34
        """       
        self.tracer = tracer(exporter=exporter, span_context=span_context)

    @cherrypy.expose
    def index(self):
        with self.tracer.span('controller.index') as span1:
            content = '[]'
            try:
                content = self._fetchPage()
            except Exception as ex:
                pass
            return content

    @cherrypy.expose
    def fetch(self):
        # return pformat(cherrypy.request.headers)
        with self.tracer.span('controller.fetch') as span1:
            content = '[]'
            try:
                content = self._httpRequest('https://jsonplaceholder.typicode.com/users').content
            except Exception as ex:
                pass
            return content

    def _fetchPage(self):
        with self.tracer.span('fetch.page') as span1:
            # try:
            #     return self._httpRequest('http://distributed-tracing-test-php2:8000/fetch').content
            # except Exception as ex:
            #     pass

            try:
                return self._httpRequest('http://distributed-tracing-test-py2:8099/fetch').content
            except Exception as ex:
                pass

        return '[]'

    def _httpRequest(self, url):
        with self.tracer.span('http.get') as span1:
            """
            @see https://github.com/census-instrumentation/opencensus-python/blob/master/opencensus/trace/propagation/trace_context_http_header_format.py#L131
            """
            headers = self.propagator.to_headers(self.tracer.span_context)
            return requests.get(url, headers=headers)

cherrypy.config.update({'server.socket_host': '0.0.0.0', 'server.socket_port': 8099})

if __name__ == '__main__':
    cherrypy.quickstart(Controller())