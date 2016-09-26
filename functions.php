<?php
if(!defined("_TASKER")) die("Permission denied");

function render($template, $params = array()) {
	$errors = $params["errors"] ? $params["errors"] : new stdClass();
	$vars = $params["vars"] ? $params["vars"] : array();
	$content_view = $template.".php";
	include_once _VIEWS_PATH.APP_VIEW;
}

function view($view, $params = array()) {
	$errors = $params["errors"] ? $params["errors"] : new stdClass();
	$vars = $params["vars"] ? $params["vars"] : array();
	include_once _VIEWS_PATH.$view.".php";
}

function redirect($url) {
	header( 'Location: '.$url );
}

function salt_password($password) {
	return md5($password._TASKER_SALT);
}

function real_escape_string($str) {
	return DB::$connection->real_escape_string($str);
}

function isForbiddenChars($str) {
	$pattern = '/[\'"`*\/\\|\[\](){}:;%!#$^&]/';
	preg_match($pattern, $str, $matches, PREG_OFFSET_CAPTURE);
	return count($matches) ? true : false;
}

function getJson() {
	$data = file_get_contents('php://input');
	return json_decode($data, true);
}

?>