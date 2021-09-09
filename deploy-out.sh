echo start launch
mv ${ENVS_PATH}/.env.${BRANCH} ${ENVS_PATH}/.env
rm ${ENVS_PATH}/.env.*
php typeInstaller.php
echo end
