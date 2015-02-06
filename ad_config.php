<?php
/*
Конфигурационный файл с настройками.

Перед использованием скрипта "AD Frontend" необходимо настройить!
*/
if ($main_var != 'parol') exit;     // защита от запуска этого файла отдельно (не изменять!)

// Настройки AD  #####################################################################

// !!! Введите FQDN контролера домена:  !!!
$ad_host = "verondc.veronet.local";

// выделяем имя домена
$ad_domain = substr(strstr($ad_host,"."),1);
// создаем DN-путь домена
$ad_dn = "DC=".implode(",DC=",explode(".", $ad_domain));
// Подготавливаем настройки подключения к домену
$ad_conf = array (
  'base_dn'=>$ad_dn,
  'account_suffix'=>'@'.$ad_domain,
  'use_tls'=>false,
  'use_ssl'=>true,
  'domain_controllers'=>array($ad_host));
 // Указываем количество попыток при неудачном вводе пароля
$fail_time = 50;
//####################################################################################


// Имя сайта (ввести просто название) - чушь какаято
$site_name = $ad_host;

// Переменные, заданные по-умолчанию
//$today = date('d.m.Y');  //Сегодняшний день
//$my_ip = $_SERVER['REMOTE_ADDR'];
if (!isset($_SESSION['admin'])) {$_SESSION['admin']=false;}

// страница по-умолчанию
$target = (isset($_SESSION['target'])) ? $_SESSION['target']:"ad_tree.php";
?>