import cherrypy

from tracer.controller.controller import Controller

cherrypy.config.update({'server.socket_host': '0.0.0.0', 'server.socket_port': 8099})

if __name__ == '__main__':
    cherrypy.quickstart(Controller())