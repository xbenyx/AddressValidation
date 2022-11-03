<?php

define( 'ROOT_DIR', dirname(dirname(__FILE__)) . '/' );

$PARAMS = array(
    "host_smtp"       => 'smtp.gmail.com',
    "email"       => "email@com",
    "pwd"       => "password",
    "emailto"       => "email@com",
    "database" => "DATABASENAME",
    "database_user" => "user",
    "database_pwd" => "userpassword",
    "ups_accesslicensenumber" => "xxxxx",
    "ups_userid" => "xxxxx",
    "ups_password" => "xxxxx*",
    "nextbillion_token" => "xxxxx"

);

$GLOBALS['PARAMS']  = $PARAMS;

define( 'PARAMS', $PARAMS );