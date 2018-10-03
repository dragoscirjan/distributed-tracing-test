import cherrypy
import logging
import requests
from pprint import pformat

from .tracer import TracerController

class Controller(TracerController):

    @cherrypy.expose
    def index(self):
        logging.debug('============== index ===============')
        logging.debug('>>>>> /index headers: %s' % pformat(cherrypy.request.headers))
        with self.getTracer(True).span('controller.index.py') as span1:
            content = '[]'
            try:
                content = self.fetchPage()
            except Exception as ex:
                logging.error(ex)
            return content

    @cherrypy.expose
    def fetch(self):
        logging.debug('============== fetch ===============')
        logging.debug('>>>>> /fetch headers: %s' % pformat(cherrypy.request.headers))
        with self.getTracer(True).span('controller.fetch.py') as span1:
            content = '[]'
            try:
                content = self.makeHttpRequest('https://jsonplaceholder.typicode.com/users').content
            except Exception as ex:
                pass
            return content

    def fetchPage(self):
        with self.getTracer().span('fetch.page'):
            try:
                return self.makeHttpRequest('http://distributed-tracing-test-py2:8099/fetch').content
            except Exception as ex:
                logging.error(ex)

        return '[]'

    def makeHttpRequest(self, url):
        logging.debug('>>>>> Trace url: %s' % url)
        with self.getTracer().span('http.get'):
            """
            @see https://github.com/census-instrumentation/opencensus-python/blob/master/opencensus/trace/propagation/trace_context_http_header_format.py#L131
            """
            headers = self.getPropagator().to_headers(self.getTracer().span_context)
            # headers['X-CLOUD-TRACE-CONTEXT'] = headers['traceparent']
            logging.debug('>>>>>> Send headers: %s' % pformat(headers))
            return requests.get(url, headers=headers)