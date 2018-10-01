from jinja2 import Template
from os import path
import argparse

def getArguments():
    parser = argparse.ArgumentParser(description='Process some integers.')
    parser.add_argument('-php', '--php-project', help='Set the php project name/folder.',
                        type=str, dest='phpProject', required=True)
    parser.add_argument('-py', '--py-project', help='Set the py project name/folder.',
                        type=str, dest='pyProject', default='')

    args = parser.parse_args()
    return args

args = getArguments()
params = {
    'project': {
        'php': args.phpProject,
        'py': args.pyProject
    }
}
print(params)

def compileDockerfilePhp():
    global args
    dir = path.dirname(path.realpath(__file__))
    fr = open(path.join(dir, 'Dockerfile.j2'), 'r')
    fw = open(path.join(dir, args.phpProject, 'Dockerfile'), 'w')
    fw.write(Template(fr.read()).render(project=args.phpProject))

def compileDockerfilePy():
    global args
    if (args.pyProject == ''): return
    dir = path.dirname(path.realpath(__file__))
    fr = open(path.join(dir, 'Dockerfile.j2'), 'r')
    fw = open(path.join(dir, args.pyProject, 'Dockerfile'), 'w')
    fw.write(Template(fr.read()).render(project=args.pyProject))

def compileDockerComposeYml():
    global params
    dir = path.dirname(path.realpath(__file__))
    fr = open(path.join(dir, 'docker-compose.yml.j2'), 'r')
    fw = open(path.join(dir, 'docker-compose.yml'), 'w')
    fw.write(Template(fr.read()).render(**params))

def compile():
    compileDockerfilePhp()
    compileDockerfilePy()
    compileDockerComposeYml()

def main():
    compile()

if __name__ == "__main__":
    main()
    
