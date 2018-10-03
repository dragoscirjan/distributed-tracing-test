
import cherrypy
import logging
import requests
from .controller import Controller

class Controller2Php(Controller):

    def fetchPage(self):
        with self.getTracer().span('fetch.page'):
            try:
                return self.makeHttpRequest('http://distributed-tracing-test-php2:8000/fetch').content
            except Exception as ex:
                logging.error(ex)

        return '[]'