# load .env
export $(cat .env | sed 's/#.*//g' | xargs)

restore_database() {
  docker-compose exec -T mysql sh -c 'pv $(find $directory -type f -name \backup.* | tail -n1) | mysql -u $MYSQL_USER --password=$MYSQL_ROOT_PASSWORD $MYSQL_DATABASE'
}

# enable running different functions from console
$@