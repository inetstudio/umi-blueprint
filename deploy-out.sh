echo start launch
mv .env.${BRANCH} .env
rm .env.*
php typeInstaller.php
composer install
echo end launch
