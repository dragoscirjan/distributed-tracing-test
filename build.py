from jinja2 import Template
from os import path
import argparse

def getArguments():
    parser = argparse.ArgumentParser(description='Process some integers.')
    parser.add_argument('-p', '--project', help='Set the php project name/folder.',
                        type=str, dest='project', required=True)
    # parser.add_argument('-php', '--php-project', help='Set the php project name/folder.',
    #                     type=str, dest='phpProject', required=True)
    # parser.add_argument('-py', '--py-project', help='Set the py project name/folder.',
    #                     type=str, dest='pyProject', default='')

    args = parser.parse_args()
    return args

args = getArguments()

def compileDockerfilePhp():
    global args
    if 'php-' not in args.project: return
    dir = path.dirname(path.realpath(__file__))
    fr = open(path.join(dir, 'docker/php/Dockerfile.j2'), 'r')
    fw = open(path.join(dir, args.project, 'docker/php/Dockerfile'), 'w')
    fw.write(Template(fr.read()).render(project=args.project))

def compileDockerfilePy():
    global args
    if 'py-' not in args.project: return
    dir = path.dirname(path.realpath(__file__))
    fr = open(path.join(dir, 'docker/py/Dockerfile.j2'), 'r')
    fw = open(path.join(dir, args.project, 'docker/py/Dockerfile'), 'w')
    fw.write(Template(fr.read()).render(project=args.project))

def compileDockerComposeYml():
    global args
    dir = path.dirname(path.realpath(__file__))
    fr = open(path.join(dir, 'docker-compose.yml.j2'), 'r')
    fw = open(path.join(dir, 'docker-compose.yml'), 'w')
    fw.write(Template(fr.read()).render(project=args.project))

def compile():
    compileDockerfilePhp()
    compileDockerfilePy()
    compileDockerComposeYml()

def main():
    compile()

if __name__ == "__main__":
    main()
    
