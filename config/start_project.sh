#!/usr/bin/env bash
echo "Подождите.Идёт создания файла"
#cat -n  >db_info.txt <<- 'EOF'
echo -n "What's your host name: "
read hostname
echo -n "What's your data base name: "
read dbname
echo -n "What's your login: "
read login
echo -n "What's your password: "
read password

cat > db_info.txt <<EOF
$hostname
$dbname
$login
$password
EOF
#echo "hostname" >> db_info.txt