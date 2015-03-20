<?php
/*
Файл с проверкой и обработкой входящих файлов
*/
if ($main_var != 'parol') exit;

// Обработка команды выхода пользователя, пришедшей GET'ом
if (isset($_GET['exit'])) {
    logout();
}


//###########################################################
//Если пришло что-то постом/гетом, то обюрабатываем
if (isset($_POST['act']) || isset($_GET['act'])) {
	
	//Проверка авторизации
	if ($_POST['act'] == "auth") {
		$input_username = trim(addslashes($_POST['login']));
		$input_userpass = trim(addslashes($_POST['pass']));
		autorization($input_username, $input_userpass);
	}
    elseif (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
        //Получить список заблокированых пользователей
        if ($_GET['act'] == "get_locked_users") {
            $aLu = get_locked_users();
            $aRes = array();
            if ($aLu) {
                foreach ($aLu as $i => $user) {
                    $aRes[$i]["title"] = $user["displayname"];
                    $aRes[$i]["key"] = $user["samaccountname"];
                    $aRes[$i]["data"]["tstamp"] = $user["lockouttime"];
                    $aRes[$i]["icon"] = "user16.png";
                    $aRes[$i]["selected"] = true;
                }
                $jRes = json_encode($aRes);
                print_r($jRes);
            } else {
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
                        echo "Ошибка разблокировки пользователя " . $user . "\n" . $adldap->getLastError();
                        exit;
                    }
                }
                $few_users = count($_GET['ul']) - 1;
                echo ($few_users) ? "Пользователи успешно разблокированыю" : "Пользователь успешно разблокирвван";
            } else {
                echo "Не передано ни одного пользователя";
            }
            exit;
        }


        //Получить дерево объектов
        if ($_GET['act'] == "get_tree") {
            $ob_type = (isset($_GET['type'])) ? $_GET['type'] : NULL;
            if (isset($_GET['pNode'])) {
                $par_node = ($_GET['pNode'] == "NULL") ? $_GET['pNode'] : explode("__", $_GET['pNode']);
                //$par_node = ($_GET['pNode']=="NULL")?$_GET['pNode']:explode("__",explode(":",$_GET['pNode'])[1]);
                echo(build_tree($par_node, $ob_type));
            }
            exit;
        }

        //Удаление пользователя
        if ($_GET['act'] == "del_users") {
            if (isset($_GET['u']) and is_array($_GET["u"])) {
                $users = $_GET['u'];
                $res = true;
                foreach ($users as $user) {
                    $del = $adldap->user()->delete($user);
                    if (!$del) $res = false;
                }
            }
            echo $res;
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
            $cont = str_replace(":", "=", explode("__", $_GET["cr_user_cont"]));
            //$cont=  explode("__", $_GET["cr_user_cont"]);
            if ($_GET["cr_user_chpas"] == "on") {
                $require_pch = 1;
            } else {
                $require_pch = 0;
            }
            if (isset($_GET["cr_user_descr"])) {
                $description = $_GET["cr_user_descr"];
            } else {
                $description = "";
            }
            $attributes = array(
                "logon_name" => mb_convert_encoding($_GET["cr_user_logon"], "Windows-1252").$ad_conf["account_suffix"],
                "username" => mb_convert_encoding($_GET["cr_user_logon"], "Windows-1252"),
                "firstname" => mb_convert_encoding($_GET["cr_user_name"], "Windows-1251", "UTF-8"),
                "surname" => iconv("UTF-8", "Windows-1252//TRANSLIT", $_GET["cr_user_surn"]),
                "email" => iconv("UTF-8", "Windows-1252//TRANSLIT", $_GET["cr_user_email"]),
                "container" => $cont,
                "enabled" => 1,
                "password" => $_GET["cr_user_pass"],
                "display_name" => $_GET["cr_user_fullname"],
                "change_password" => $require_pch
            );
            //echo "cont=". $cont;

            //print_r($cont);

            $result = $adldap->user()->create($attributes);
            if ($result) {
                echo "true";
            } else {
                echo "false";
            }
            exit;
        }
    }
    else {
       header("Location: index.php", true, 200);
    }

}
