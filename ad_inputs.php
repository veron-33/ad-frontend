<?php
/*
Файл с проверкой и обработкой входящих файлов
*/
if ($main_var != 'parol') exit;

// Обработка команды выхода пользователя, пришедшей GET'ом
if (isset($_GET['exit'])) {
	$_SESSION['admin'] = false;
	$adldap->close();
	$_SESSION['admin'] = "";
	$_SESSION['user_login'] = "";
	//session_destroy();
}


//###########################################################
//Если пришло что-то постом/гетом, то обюрабатываем
if (isset($_POST['act']) || isset($_GET['act'])) {
	
	//Проверка авторизации
	if ($_POST['act'] == "autorization") {
		$input_username = trim(addslashes($_POST['username']));
		$input_userpass = trim(addslashes($_POST['userpass']));
		autorization($input_username, $input_userpass);
	}


    //Получить список заблокированых пользователей
    if ($_GET['act'] == "get_locked_users") {
        $aLu = get_locked_users();
        $aRes =array();
        if ($aLu) {
            foreach ($aLu as $i=>$user) {
                $aRes[$i]["title"] = $user["displayname"];
                $aRes[$i]["key"] = $user["samaccountname"];
                $aRes[$i]["data"]["tstamp"] = $user["lockouttime"];
                $aRes[$i]["icon"] = "user16.png";
                $aRes[$i]["selected"] = true;
            }
            $jRes = json_encode($aRes);
            print_r($jRes);
        }
        else {
            echo "false";
        }
        exit;
    }


    //Разблокировать пользователей
    if ($_GET['act'] == "unlock_users") {
        if (isset($_GET['ul'])) {
            foreach ($_GET['ul'] as $user) {
                $unlock = unlock_user($user);
                if (!$unlock) {
                    echo "Ошибка разблокировки пользователя ".$user."\n".$adldap->getLastError();
                    exit;
                }
            }
            $few_users= count($_GET['ul']) - 1;
            echo ($few_users)?"Пользователи успешно разблокированыю":"Пользователь успешно разблокирвван";
        }
        else {
            echo "Не передано ни одного пользователя";
        }
        exit;
    }


    //Получить дерево объектов
    if ($_GET['act'] == "get_tree") {
        $ob_type = (isset($_GET['type']))?$_GET['type']:NULL;
        if (isset($_GET['pNode'])) {
            $par_node = ($_GET['pNode']=="NULL")?$_GET['pNode']:explode("__",$_GET['pNode']);
            //$par_node = ($_GET['pNode']=="NULL")?$_GET['pNode']:explode("__",explode(":",$_GET['pNode'])[1]);
            echo (build_tree($par_node, $ob_type));
        }
        exit;
    }


    //Создать нового пользователя
    if (($_GET['act'] == "cr_user")
        and (isset($_GET["cr_user_surn"]))
        and (isset($_GET["cr_user_name"]))
        and (isset($_GET["cr_user_fullname"]))
        and (isset($_GET["cr_user_logon"]))
        and (isset($_GET["cr_user_email"]))
        and (isset($_GET["cr_user_pass"]))
        and (isset($_GET["cr_user_cont"]))
    ) {

        $cont= str_replace(":", "=", explode("__", $_GET["cr_user_cont"]));
        //$cont=  explode("__", $_GET["cr_user_cont"]);
        $attributes=array(
            "username"=>mb_convert_encoding($_GET["cr_user_logon"], "Windows-1252"),
            "firstname"=>mb_convert_encoding($_GET["cr_user_name"], "Windows-1251", "UTF-8"),
            "surname"=>iconv("UTF-8","Windows-1252//TRANSLIT",$_GET["cr_user_surn"]),
            "email"=>iconv("UTF-8","Windows-1252//TRANSLIT",$_GET["cr_user_email"]),
            "container"=>$cont,
            "enabled"=>1,
            "password"=>$_GET["cr_user_pass"],
            "display_name"=>$_GET["cr_user_fullname"]
        );
        //echo "cont=". $cont;

        //print_r($cont);

        $result = $adldap->user()->create($attributes);
        if ($result) {
            echo "true";
        }
        else {
            echo "false";
        }
        exit;
    }



}



//############################################################