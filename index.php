<?php
define("_TASKER", true);
include_once './config.php';
include_once './functions.php';

/* Import all the controllers */
foreach (glob(_CONTROLLERS_PATH."*.php") as $filename) {
    include_once $filename;
}

DB::connect();
if (DB::$connection->connect_errno) {
    $message = "Ошибка: Не удалсь создать соединение с базой MySQL и вот почему: <br>";
    $message .= "Номер_ошибки: " . $db->connect_errno . "<br>";
    $message .= "Ошибка: " . $db->connect_error;
    render("500", ["message" => $message]);
    exit;
}

Auth::check();

include_once _SITE_PATH."/routes.php";
?>