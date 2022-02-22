echo start launch
mv .env.${BRANCH} .env
rm .env.*
php typeInstaller.php
php composer install
echo end
