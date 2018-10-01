import cherrypy
import requests
from opencensus.trace import config_integration
from opencensus.trace.exporters.jaeger_exporter import JaegerExporter
from opencensus.trace.propagation.trace_context_http_header_format import TraceContextPropagator
# from opencensus.trace.tracers.context_tracer import ContextTracer
from opencensus.trace.tracer import Tracer


class Controller(object):
    def __init__(self):
        tracer = Tracer
        # tracer = ContextTracer

        exporter = JaegerExporter(
            agent_host_name='distributed-tracing-test-jaeger',
            # host_name='distributed-tracing-test-jaeger',
            service_name='my-jaeger-py'
        )
        # exporter = None

        config_integration.trace_integrations(['httplib'])
        propagator = TraceContextPropagator()

        self.trace = tracer(exporter=exporter, propagator=propagator)

    @cherrypy.expose
    def index(self):
        content = ''
        with self.trace.span('jsonplaceholder_users'):
            r = requests.get('https://jsonplaceholder.typicode.com/users')
            content = r.content
        return content

cherrypy.config.update({'server.socket_host': '0.0.0.0', 'server.socket_port': 8099})

if __name__ == '__main__':
    cherrypy.quickstart(Controller())