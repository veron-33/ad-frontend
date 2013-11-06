<?php
/*
Файл с проверкой и обработкой входящих файлов
*/
if ($main_var != 'parol') exit;

// Обработка команды выхода пользователя, пришедшей GET'ом
if (isset($_GET['exit'])) {
	$_SESSION['admin'] = false;
	$adldap->close();
	//unset($_SESSION['admin']);
	//unset($_SESSION['user_login']);
	//session_destroy();
	}

// Если гетом пришла цель на страницу, то проверяем ее существование
// и, если существует, переходим на нее.
if (isset($_GET['p'])) {
	$target_с = "ad_".$_GET['p'].".php";
	if (file_exists($target_с)) {
		$_SESSION['target'] = $target_с;
		$target = $target_с;
	}
	else {
		$error_text = "Запрашмваемая страница не найдена. Не балйутесь.";
	}		
}


//###########################################################
//Если пришло что-то постом/гетом, то обюрабатываем
if (isset($_POST['act'])) {
	
	//Проверка авторизации
	if ($_POST['act'] == "autorization") {
		$input_username = trim(addslashes($_POST['username']));
		$input_userpass = trim(addslashes($_POST['userpass']));
		autorization($input_username, $input_userpass);
	}	
}


//############################################################
?>