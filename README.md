# umi-blueprint
<p align="center"><img src="https://www.umi-cms.ru/templates/umi/images/main_logo.png?2021" alt="umi_logo"></p>

<p><strong>Prepare UMI environment</strong></p>

- change the folder template name of you `PROJECT` inside _templates_ folder
- `config.ini` *inside* `PROJECT` folder -- replace `PROJECT` with the name of you project
- all resources (js/css/fonts/images etc.) are placed in `../public/` folder >> inside `PROJECT` folder
- `sitePhpExtension` class -- replace `PROJECT` with correct settings _"container_id"_
- add all dependencies to `composer.json` and run `composer install` (in project root folder)

<p><strong>Prepare virtual machine</strong></p>

- install docker https://docs.docker.com/engine/install/
- run command in terminal `cp .env.local .env` (in project root folder)
- `.env` -- replace `placeholder` data with the real values
- start docker in terminal -- `docker-compose up -d` (in project root folder)

<p><strong>Prepare CI/CD environment</strong></p>

- `.git-ftp-ignore` -- replace `PROJECT` with the name of you project folder
- `.gitignore` -- replace `PROJECT` with the name of you project folder
- add `PROD_USERNAME` environment variable inside VCS system (bitbucket.org)
- `bitbucket-pipelines.yml` -- replace `PROJECT` with the name of you project folder / domain name of you dev env
- after init commit change _deploy mode_ for your env branch `DEP_MODE="init -v --insecure"` from `init` to `push`

<p><strong>Prepare Sentry.io monitoring integration</strong></p>

- create 2 separate projects for `frontend` and `backend` teams with the `PROJECT-frontend/backend` names
- add `VERSION_POSTFIX` environment variable inside _VCS_ system (bitbucket.org) which will represent your `PROJECT` name
