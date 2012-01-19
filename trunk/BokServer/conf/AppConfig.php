<?php

class AppConfig {

    const DB_HOST_NAME="localhost";
    const DB_USER="root";
    const DB_PASSWORD="";
    const DB_NAME="knubo";

    #Set to 1 if you want authentication.
    const USE_AUTHENTICATION=1;

    const MYSQLDUMP="/usr/local/mysql/bin/mysqldump";
    const WGET='/usr/local/bin/wget';

    #Common db prefix for all database.
    const DB_PREFIX = "bok_";

    function prefix() {
        
        if(strncmp($_SERVER["SERVER_NAME"], "knutbok",7) == 0) {
            return "knut_";
        }
        
        return "bok_";
    }

}
?>
