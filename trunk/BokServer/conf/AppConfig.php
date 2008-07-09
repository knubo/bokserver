<?php


class AppConfig {

const DB_HOST_NAME="localhost";
const DB_USER="root";
const DB_PASSWORD="";
const DB_NAME="knubo";

#Set to 1 if you want authentication.
const USE_AUTHENTICATION=0;

const MYSQLDUMP="/usr/local/mysql/bin/mysqldump";

#Common db prefix for all database.
const DB_PREFIX = "bok_";
}
?>
