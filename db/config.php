<?php

return array(
    "db" => "mysql:host=localhost;dbname=asteriskcdrdb",
    "username" => "root",     //Mysql login
    "password" => "password", //Mysql password
    "options" => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'),
    "asterisk_ip" => "127.0.0.1",
    "manager_port" => "5038",
    "manager_login" => "admin",
    "manager_password" => "password",
    "monitor" => "/var/spool/asterisk/monitor/",   
    "users" => array('admin' => 'password', 'guest' => 'guest')
);


