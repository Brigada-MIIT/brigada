<?php

require '../core/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('db_host', $_ENV['DATABASE_HOST']);
define('db_user', $_ENV['DATABASE_USERNAME']); 
define('db_basename', $_ENV['DATABASE_BASENAME']);
define('db_password', $_ENV['DATABASE_PASSWORD']);

define('mysql_port', '3306');

ini_set('allow_url_fopen', 1);
date_default_timezone_set('Europe/Moscow');

?>