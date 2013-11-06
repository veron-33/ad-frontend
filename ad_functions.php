<?php
/*
Файл с функциями
*/
if ($main_var != 'parol') exit;     // защита от запуска этого файла отдельнo

// Пытаемся соединиться с сервером AD
try {
	  $adldap = new adLDAP($ad_conf);
}
catch (adLDAPException $e) {
	  echo $e; 
	  exit();   
}

// Функция проверки авторизации
function autorization($usern, $userp) {
  global $error_text, $fail_time, $ad_host, $ad_domain, $ad_dn, $ad_conf, $adldap;
  if (!isset($_SESSION['login_failed'])) {
	  // авторизуемся
	  $_SESSION['admin'] =($adldap->authenticate($usern, $userp))?true:false;
	  if ($_SESSION['admin']) {
		  // вытаскиваем инфу о пользователе
		  $userinfo = $adldap->user()->infoCollection($usern);
		  $_SESSION['cur_username'] = $userinfo->displayName;
		  $_SESSION['usern'] = $usern;
		  $_SESSION['userp'] = $userp;
	  }
	  else {
		  // Ставим проверку для запрета подбора пароля.
		  if (isset($_SESSION['login_fail'])) {
			  $_SESSION['login_fail']++;
		  }
		  else {
			  $_SESSION['login_fail'] = 1;
		  }
		  $error_text = "Ошибка при вводе логина/пароля! Осталось попыток: ".($fail_time-$_SESSION['login_fail']);
		  if ($_SESSION['login_fail'] == $fail_time) {
			  $_SESSION['login_failed'] = true;
			  $error_text = "Вы исчерпали допустимое количество попыток ввода пароля! Ждите...";
		  }
	  }
  }
  else {$error_text = "Вы исчерпали допустимое количество попыток ввода пароля! Ждите...";}
}

// Устанавливаем подключение к контрлеру домену в случае установленной сессии
if ($_SESSION['admin']) autorization($_SESSION['usern'], $_SESSION['userp']);


function get_cn($dn) {
	$dn_arr = explode(",",$dn);
	$cn_arr = explode("=",$dn_arr[0]);
	return $cn_arr[1];
}
function get_node_type ($dn) {
	$ntype_arr = end($dn);
	return $ntype_arr;
}



// dynatree дерево с подгрузской данных (arg: путь поиска, тип объекта для поиска,[проверка на наличие детей])
function build_tree($foldername, $ob_type ,$check_child = false) {
	global $adldap;
	$folders = $adldap->folder()->listing($foldername, "oucn", false, $ob_type);
	if ($foldername=="NULL") $foldername = array();
	if ($folders["count"]==0 && $check_child) return false;
	else if ($check_child) return true;
	$result = array();
	for ($i=0; $i < $folders["count"]; $i++) {
		$curr_cn = get_cn($folders[$i]["dn"]);
		$curr_type = get_node_type($folders[$i]["objectclass"]);
		$result[$i] = array("title"=>$curr_cn);
		if ($ob_type=="folders") $result[$i]["folder"] = "true";
		else $result[$i]["icon"] = "". $curr_type ."16.png";
		$nf = $foldername;
		array_unshift($nf, $curr_cn);
		if (build_tree($nf, $ob_type, true)) {
			$result[$i]["lazy"] = "true";
		}
		$result[$i]["key"]=implode("__",$nf);
	}        
	$result =  json_encode($result);    
	return $result;
}




// Функция работающая с БД
function sql ($sql) {
  global $error_text, $connected, $db_host, $db_user, $db_pass, $db_name;
  // Подключаемся к БД, если еще не подключены
  if (!isset($connected)) {
	  $connected = mysql_connect($db_host, $db_user, $db_pass);
	  mysql_select_db($db_name);	
	  //mysql_query("SET NAMES cp1251");
  }
  $query = mysql_query($sql);
  if (mysql_errno() != 0) {
	  $error_text = "Ошибка №".mysql_errno()." при работе с БД: ".mysql_error();
  }
  return $query;
}




?>