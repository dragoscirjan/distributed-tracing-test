from jinja2 import Template
from os import path
import argparse

def getArguments():
    parser = argparse.ArgumentParser(description='Process some integers.')
    parser.add_argument('-p', '--project', help='Set the project name/folder.',
                        type=str, dest='project', required=True)

    args = parser.parse_args()
    return args

args = getArguments()

def compileDockerfile():
    global args
    dir = path.dirname(path.realpath(__file__))
    fr = open(path.join(dir, 'Dockerfile.j2'), 'r')
    fw = open(path.join(dir, args.project, 'Dockerfile'), 'w')
    fw.write(Template(fr.read()).render(project=args.project))

def compile():
    compileDockerfile()

def main():
    compile()

if __name__ == "__main__":
    main()
    
