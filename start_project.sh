#!/usr/bin/env bash
#echo "Подождите.Идёт создания файла"
#cat -n  >db_info.txt <<- 'EOF'
echo -n "What's your host name: "
read hostname
#echo -n "What's your data base name: "
#read dbname
echo -n "What's your login: "
read login
echo -n "What's your password: "
read password
#The name of the database is not changed by the user and is already set as ready
dbname="blog_dogs"
cat > config/db_info.txt <<EOF
$hostname
$dbname
$login
$password
EOF
#echo "hostname" >> db_info.txt

#start script create bd
cd config
php createDB.php
php CreateYML.php
cd ..
php vendor/bin/phinx migrate -e production