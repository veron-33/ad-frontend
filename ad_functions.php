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
	global $error_text, $fail_time, $adldap;
	if (!isset($_SESSION['login_failed'])) {
	  // авторизуемся
	  $_SESSION['admin'] =($adldap->authenticate($usern, $userp))?true:false;
	  if ($_SESSION['admin']) {
		  // вытаскиваем инфу о пользователе
		  $userinfo = $adldap->user()->infoCollection($usern);
		  $_SESSION['cur_username'] = $userinfo->displayName;
		  $_SESSION['usern'] = $usern;
		  $_SESSION['userp'] = $userp;
		  $_SESSION['login_fail'] = 0;
	  }
	  else {
		  $er = $adldap->getLastError();
		  if ($er == "Can't contact LDAP server") {
			  $error_text = "Невозможно соединиться с контроллером домена!";
		  }
		  else {
			  // Ставим проверку для запрета подбора пароля.
			  if (isset($_SESSION['login_fail'])) {
					  $_SESSION['login_fail']++;
				  }
			  else {
					  $_SESSION['login_fail'] = 1;
				  }
			  if ($_SESSION['login_fail'] == $fail_time) {
				  $_SESSION['login_failed'] = true;
				  $error_text = "Вы исчерпали допустимое количество попыток ввода пароля! Ждите с моря погоды...";
			  }
			  else {
				  $error_text = "Ошибка при вводе логина/пароля! Осталось попыток: ".($fail_time-$_SESSION['login_fail']);
			  }
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

function get_cont_type($dn) {
	$dn_arr = explode(",",$dn);
	$ct_arr = explode("=",$dn_arr[0]);
	return $ct_arr[0];
}

function get_node_type ($dn) {
	$ntype_arr = end($dn);
	return $ntype_arr;
}


/**
 * dynatree дерево с подгрузской данных
 * @param $foldername - путь поиска
 * @param $ob_type - тип объекта для поиска
 * @param bool $check_child - проверка на наличие "детей"
 * @return array|bool|string
 */
function build_tree($foldername, $ob_type ,$check_child = false) {
	global $adldap;
	if ($foldername=="NULL") {
		$foldername = array();
		$path = "NULL";
	} else {
		$path = array();
		foreach ($foldername as $value) {
			$path[] = explode(":",$value)[1];
		}
	}

	$folders = $adldap->folder()->listing($path, "oucn", false, $ob_type);
	if ($folders["count"]==0 && $check_child) return false;
	else if ($check_child) return true;
	$result = array();
	//print_r($folders);
	//return false;
	for ($i=0; $i < $folders["count"]; $i++) {
		$curr_cn = get_cn($folders[$i]["dn"]);
		$curr_ct = "CN";
		$curr_type = get_node_type($folders[$i]["objectclass"]);
		$result[$i] = array("title"=>$curr_cn);
		if ($ob_type=="folders") {
			$result[$i]["folder"] = "true";
			$curr_ct=get_cont_type($folders[$i]["dn"]);
		}
		else $result[$i]["icon"] = "". $curr_type ."16.png";
		$nf = $foldername;
		array_unshift($nf, $curr_ct.":".$curr_cn);
		if (build_tree($nf, $ob_type, true)) {
			$result[$i]["lazy"] = "true";
		}
		// key = <CN or OU>:<node>__<CN or OU>:<parent1>__<...>...
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