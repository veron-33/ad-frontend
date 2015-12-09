<?php
/*
Конфигурационный файл с настройками.

Перед использованием скрипта "AD Frontend" необходимо настройить!
*/
if ($main_var != 'parol') exit;     // защита от запуска этого файла отдельно (не изменять!)

// Настройки AD  #####################################################################

$dcs = array(
    "verondc.veronet.local",
    "dc1.s-tech.ru",
    "cyclop.s-tech.ru"
);

$salt = "aSEwKa"; // Используется для шифрования пароля в cookie


 // Указываем количество попыток при неудачном вводе пароля
$fail_time = 10;
//####################################################################################


if (!isset($_SESSION['admin'])) {$_SESSION['admin']=false;}

?>
