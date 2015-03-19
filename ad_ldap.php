<?php
session_start();
error_reporting(E_ALL);
// защита от запуска этого файла отдельно
if ($main_var != 'parol') {
    exit;
}

//Засекаю время
$S_TIME=time()+microtime();

// Включаем библиотеку adLDAP
include (dirname(__FILE__) . "/adldap/adLDAP.php");

// Включаем файл конфигурации
include ("ad_config.php");

// Включаем файл с функциями
include ("ad_functions.php");

// Включаем файл с обработкой входящих данных
include ("ad_inputs.php");

// Включаем файл основного "тела"
include ("ad_body.php");

