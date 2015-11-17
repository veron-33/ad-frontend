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
        $ad_host = $_POST["dc"];
        $_SESSION['dc'] = $ad_host;
        // выделяем имя домена
        $ad_domain = substr(strstr($ad_host,"."),1);
        // создаем DN-путь домена
        $ad_dn = "DC=".implode(",DC=",explode(".", $ad_domain));
        // Подготавливаем настройки подключения к домену
        $_SESSION["ad_conf"] = array (
            'base_dn'=>$ad_dn,
            'account_suffix'=>'@'.$ad_domain,
            'use_tls'=>false,
            'use_ssl'=>true,
            'domain_controllers'=>array($ad_host));
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


        //Cмена пароля пользователя
        if (($_POST['act'] == "change_pass")
            and (isset($_POST['newpass']))
            and (isset($_POST['user']))
        ) {
            if ($_POST['newpass'] != $_POST['newpass2']) {
                echo "Пароли не совпадают";
                exit;
            }
            $result = $adldap->user()->password($_POST['user'],$_POST['newpass']);
            echo $result;
            exit;
        }


        if (($_GET['act']=="get_user_prop") and (isset($_GET['user']))) {
            //указываем список необходимых нам полей.
            //primarygroupid необходим для получения группы по умолчанию, которой нет в memberOf
            $fields = array(
                "sn",
                "givenName",
                "displayName",
                "sAMAccountName",
                "company",
                "department",
                "physicalDeliveryOfficeName",
                "title",
                "telephoneNumber",
                "mail",
                "description",
                "memberof",
                "primarygroupid"
            );
            $result = $adldap->user()->info($_GET["user"],$fields);
            // ищем и исключаем из массива ненужные служебные поля
            $res_count = $result[0]["count"];
            for ($i=0; $i < $res_count; $i++) {
                if ($result[0][$i] == "objectsid" || $result[0][$i] == "primarygroupid") {
                    unset($result[0][$result[0][$i]]);
                    $result[0]["count"]--;
                }
                unset($result[0][$i]);
            }
            // преобразуем список групп для работы с fancytree
            unset($result[0]["memberof"]["count"]);
            foreach ($result[0]["memberof"] as $key=>$group) {
                unset($result[0]["memberof"][$key]);
                $result[0]["memberof"][$key]["title"] = get_cn($group);
                $result[0]["memberof"][$key]["key"] = $group;
                $result[0]["memberof"][$key]["icon"] = "group16.png";
            }
            echo(json_encode($result));
            exit;
        }


        //Создать нового пользователя
        if (($_POST['act'] == "cr_user")
            and (isset($_POST["cr_user_surn"]) and strlen($_POST["cr_user_surn"])>0)
            and (isset($_POST["cr_user_name"]) and strlen($_POST["cr_user_name"])>0)
            and (isset($_POST["cr_user_fullname"]) and strlen($_POST["cr_user_fullname"])>0)
            and (isset($_POST["cr_user_logon"]) and strlen($_POST["cr_user_logon"])>0)
            and (isset($_POST["cr_user_pass"]) and strlen($_POST["cr_user_pass"])>0)
            and (isset($_POST["cr_user_cont"]) and strlen($_POST["cr_user_cont"])>0)
        ) {
            $cont = str_replace(":", "=", explode("__", $_POST["cr_user_cont"]));
            //$cont=  explode("__", $_POST["cr_user_cont"]);
            if ($_POST["cr_user_chpas"] == "on") {
                $require_pch = 1;
            } else {
                $require_pch = 0;
            }
            if (isset($_POST["cr_user_descr"])) {
                $description = $_POST["cr_user_descr"];
            } else {
                $description = "";
            }
            $attributes = array(
                "logon_name" => $_POST["cr_user_logon"].$ad_conf["account_suffix"],
                "username" => $_POST["cr_user_logon"], "Windows-1252",
                "firstname" => $_POST["cr_user_name"],
                "surname" => $_POST["cr_user_surn"], "Windows-1252",
                "email" => $_POST["cr_user_email"],
                "container" => $cont,
                "enabled" => 1,
                "password" => $_POST["cr_user_pass"],
                "display_name" => $_POST["cr_user_fullname"],
                "change_password" => $require_pch,
                "office" => $_POST["cr_user_cab"],
                "title" => $_POST["cr_user_dol"],
                "telephone" => $_POST["cr_user_tel"],
                "company" => $_POST["cr_user_org"],
                "description" => $_POST["cr_user_descr"],
                "department" => $_POST["cr_user_otd"]
            );
            //echo "cont=". $cont;

            //print_r($cont);

            $result = $adldap->user()->create($attributes);
            if ($result === true) {
                //echo build_tree(explode("__", $_POST["cr_user_cont"]), "objects");
                echo true;
            } else {
                echo $result;
            }
            exit;
        }
    }
    else {
       header("Location: index.php", true, 200);
    }
}


