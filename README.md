# umi-blueprint
<p align="center"><img src="https://www.umi-cms.ru/templates/umi/images/main_logo.png?2021" alt="umi_logo"></p>

1. install [docker](https://docs.docker.com/engine/install/)

    - on Windows turn on your installed WSL distribution in Docker: _**settings** ~> resources_
    - check that your BIOS settings has a **Virtualization** option and it's `enabled`

2. run command in terminal `cp .env.local .env` _(in project root folder)_

3. `.env` -- replace `APP_NAME`, `COMPOSE_PROJECT_NAME` to your app name; replace `DB_HOST` to `mysql`
    ### Example:
    ```bash
    APP_NAME=umi-blueprint
    DB_HOST=mysql
    COMPOSE_PROJECT_NAME=umi-blueprint.test
    ```

4. IF you need NODE / REDIS services for development, add this to `docker-compose.yml` >> `services` section
    ```yaml
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

    > For REDIS your also need to add `nginx` dependency
    ```yaml
      nginx:
        depends_on:
          ...
          - redis
    ```
    > IF your containers already running (check statuses with `docker-compose ps`) and you add services \
    rebuild them with `docker-compose up -d --build` \
    IF you need a certain version of NODE, specify it in config \
    `/docker/node/Dockerfile` >> `FROM node:specific_version_number` _(autocomplete will give you all options)_

    Docker generates dynamic ports every time when you build containers.
    you can add _(in project root folder)_ `docker-compose.override.yml` to make your project ports static. So after container's rebuild you will always see your project on the certain port:

    ```yaml
    # Example of docker-compose.override.yml
    services:
      nginx:
        ports:
          - 7000:80

      mysql:
        ports:
          - 7706:3306
      # you may need it for laravel projects in case of user rights error
      php:
        user: "1000:1000"

    # `7000` is a desired port.
    ```

5. add `COMPOSE_PROJECT_NAME` *value* from `.env` to your `hosts` file as `127.0.0.1 project_name_you_choose`

6. change the folder template name of you `PROJECT` inside _templates_ folder

7. start docker in terminal -- `docker-compose up -d` _(in project root folder)_

8. type in terminal `docker-compose ps` and get `port` from `nginx` service (or use `docker-compose.override` values).

9. project will be available by the address `http://project_name_you_choose:port`

    in case of 403 error go to nginx cli in docker desktop and type `chmod -R 775 [folder name]`

10. **Import DB** (_you have 2 options_)
    > containers need to be running. check statuses with `docker-compose ps`

    > ## First option - command line
    > - place your DB dump (rename it as `backup`) into folder `/docker/mysql/dump/` \
        Support all default extensions `*.sql/*.sql.gz/*.tgz`
    >> if you have problems with import archived mysql files then use `gunzip` on file to unzip it first
    > - use command terminal with `bash` (PowerShell / GitBash on Windows) support and run commands:
    > ```sh
    >  > . /docker/mysql/backup.sh (`.` is a command)
    >  > restore_database
    > ```
    >
    > - wait until progress bar finish at 100%

    **OR**

    > ## Second option - manual import
    > You can add your database dump manually.
    > - set database connection via any database gui (Workbench, MyAdmin, Navicat, etc.): \
        get credentials from `.env` + `docker-compose ps` and get `port` from `mysql`
    > - import your DB dump to database

12. install [UMI](https://www.umi-cms.ru/downloads/) in _trial_ mode, `without template`

    - during the installation fill DB host field as `mysql` and set DB credentials from your `.env` file

## <p><strong>After UMI installed</strong></p>

- `config.ini` *inside* `PROJECT` folder -- replace `PROJECT` with the name of you project
- root `config.ini` in *[connections]* section -- change _mysql_ host to `core.host = "mysql"`
- `templates/PROJECT/classes/sitePhpExtension.php` -- replace `PROJECT` inside with correct settings _"container_id"_

- add all dependencies to `composer.json` and run `docker-compose exec php composer install` _(in project root folder)_

- all resources (js/css/fonts/images etc.) are placed in `templates/PROJECT/public/`. Run commands to build frontend _(if exist)_.

## <p><strong>Prepare CI/CD environment</strong></p>

- `.gitignore` -- replace `PROJECT` with the name of you project folder
- `.git-ftp-ignore` -- replace `PROJECT` with the name of you project folder

> frontend **can skip** this step `>>`

### Bitbucket.org
- add `PROD_USERNAME` environment variable inside VCS system
- `bitbucket-pipelines.yml` -- replace `PROJECT` with the name of you project folder / domain name of you dev env

### GitHub.com
- add `DEPLOY_USER` / `DEPLOY_KEY` (private) and `SLACK_BOT_TOKEN` to secrets section
- `.github/workflows/deploy-pipelines.yml` -- replace `PROJECT` with the name of you project folder / domain name of you dev env
- fill all necessary variables in `env` section

After init commit change _deploy mode_ for your env branch `DEP_MODE="init -v --insecure"` from `init` to `push`

> `<<` frontend **can skip** this step
## <p><strong>Prepare Sentry.io monitoring integration</strong></p> <span style="color:red">Temporary inactive</span>

- create 2 separate projects for `frontend` and `backend` teams with the `PROJECT-frontend/backend` names
- add `VERSION_POSTFIX` environment variable inside _VCS_ system (bitbucket.org) which will represent your `PROJECT` name
