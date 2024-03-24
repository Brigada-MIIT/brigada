<?php
define('db_host', 'localhost');
define('db_user', 'brigada'); 
define('db_basename', 'brigada');
define('db_password', 's194GJ!18xMrTaq9^');
define('secret_key', 'KaX81mRRtEu26j_40__49');

define('mysql_port', '3306');

ini_set("allow_url_fopen", 1);
date_default_timezone_set('Europe/Moscow');

$roles = array(
	1 => "Test",
);

$permissions_for_roles = array(
	1 => array(
		"DASHBOARD" => TRUE,
	)
);
?>