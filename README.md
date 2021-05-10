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
- all resources need to be placed inside `../public/` folder
- `sitePhpExtension` class -- replace `PROJECT` with correct settings _"container_id"_