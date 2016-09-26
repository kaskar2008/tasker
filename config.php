<?php
if(!defined("_TASKER")) die("Permission denied");
session_start();

// Salt for passwords
define("_TASKER_SALT", "5I8g9lQrzc");

define("_SITE_PATH", $_SERVER["DOCUMENT_ROOT"]);
define("_VIEWS_PATH", _SITE_PATH."/views/");
define("_CONTROLLERS_PATH", _SITE_PATH."/controllers/");

define("APP_VIEW", "app.php");

const _CONF_MYSQL = array(
	"host" => "localhost",
	"port" => 3306,
	"username" => "root",
	"password" => "",
	"database" => "tasker"
);

?>