# umi-blueprint
<p align="center"><img src="https://www.umi-cms.ru/templates/umi/images/main_logo.png?2021" alt="umi_logo"></p>

## <p><strong>Prepare UMI environment</strong></p>

- change the folder template name of you `PROJECT` inside _templates_ folder
- `config.ini` *inside* `PROJECT` folder -- replace `PROJECT` with the name of you project
- root `config.ini` in *[connections]* section -- change _mysql_ host to `core.host = "mysql"`
- all resources (js/css/fonts/images etc.) are placed in `../public/` folder >> inside `PROJECT` folder
- `sitePhpExtension` class -- replace `PROJECT` with correct settings _"container_id"_
- add all dependencies to `composer.json` and run `composer install` _(in project root folder)_

## <p><strong>Prepare virtual machine</strong></p>

- install [docker](https://docs.docker.com/engine/install/)
- run command in terminal `cp .env.local .env` _(in project root folder)_
- `.env` -- replace `placeholder` data with the real values (copy DB credentials _from / to_ `config.ini`)
- start docker in terminal -- `docker-compose up -d` _(in project root folder)_

> IF you need NODE / REDIS services for development \
> add this to `docker-compose.yml` >> `services` section
``` bash
  redis:
    image: redis
    container_name: ${COMPOSE_PROJECT_NAME}-redis
    ports:
      - 6379
    <<: *networks

  node:
    build:
      context: ./docker/node/
      dockerfile: ./Dockerfile
    container_name: ${COMPOSE_PROJECT_NAME}-node
    volumes:
      - ./:/var/www/${COMPOSE_PROJECT_NAME}
    <<: *working_dir
```
> IF your containers already running (check statuses with `docker-compose ps`) and you add services \
> Stop them with `docker-compose stop`, then build them again with `docker-compose build` \
> and run `docker-compose up -d`

> IF you need a certain version of NODE, specify it in config \
> `/docker/node/Dockerfile` >> `FROM node:specific_version_number` _(autocomplete will give you all options)_

## <p><strong>Import DB</strong></p>

> containers need to be running. check statuses with `docker-compose ps`
- place your DB dump into folder `/docker/mysql/dump/`. Support all default extensions `*.sql/*.sql.gz/*.tgz`
- use command terminal with `bash` (PowerShell / GitBash on Windows) support and run commands:
> . /docker/mysql/backup.sh (`.` is a command)\
> restore_database
- wait until progress bar finish at 100%

## <p><strong>Prepare CI/CD environment</strong></p>

- `.git-ftp-ignore` -- replace `PROJECT` with the name of you project folder
- `.gitignore` -- replace `PROJECT` with the name of you project folder
- add `PROD_USERNAME` environment variable inside VCS system (bitbucket.org)
- `bitbucket-pipelines.yml` -- replace `PROJECT` with the name of you project folder / domain name of you dev env
- after init commit change _deploy mode_ for your env branch `DEP_MODE="init -v --insecure"` from `init` to `push`

## <p><strong>Prepare Sentry.io monitoring integration</strong></p>

- create 2 separate projects for `frontend` and `backend` teams with the `PROJECT-frontend/backend` names
- add `VERSION_POSTFIX` environment variable inside _VCS_ system (bitbucket.org) which will represent your `PROJECT` name
