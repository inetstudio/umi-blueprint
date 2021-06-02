# umi-blueprint
<p align="center"><img src="https://www.umi-cms.ru/templates/umi/images/main_logo.png?2021" alt="umi_logo"></p>

<p><strong>Prepare CI/CD environment</strong></p>

- `.git-ftp-ignore` -- replace `PROJECT` with the name of you project folder
- `.gitignore` -- replace `PROJECT` with the name of you project folder
- `bitbucket-pipelines.yml` -- replace `PROJECT` with the name of you project folder / domain name of you dev env
- after init commit change _deploy mode_ for you env branch `DEP_MODE="init -v --insecure"` from `init` to `push`

<p><strong>Prepare UMI environment</strong></p>

- change the folder template name of you `PROJECT` inside _templates_ folder
- `config.ini` -- replace `PROJECT` with the name of you project folder
- all resources (js/css/fonts/images etc.) are placed in `../public/` folder >> inside `PROJECT` folder
- `sitePhpExtension` class -- replace `PROJECT` with correct settings _"container_id"_
- add all dependencies to `composer.json` and run `composer intall` in root folder

<p><strong>Prepare virtual machine</strong></p>

- `.env.local` -- replace `placeholder` values with the real data
- cp .env.local .env
- docker-compose up -d