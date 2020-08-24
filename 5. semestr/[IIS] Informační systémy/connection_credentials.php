<?php

    $host = "localhost";

    $username = "xchlad16";
    $password = "utuj5isu";

    $dbname = "xchlad16";
    $port = 0;
    $socket = '/var/run/mysql/mysql.sock';

    $dsn = "mysql:host=" . $host . ";dbname=" . $dbname .";port=/var/run/mysql/mysql.sock";
    
    $options = array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    );

?>