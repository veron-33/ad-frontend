<?php
/*
Файл шапки страницы
*/
if ($main_var != 'parol') exit;     // защита от запуска этого файла отдельнo
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
        <link rel="icon" href="favicon.ico" type="image/x-icon"/>
        <link type="text/css" href="css/jquery-ui.css" rel="stylesheet"
        /
        <!-- include jquery libs -->

        <script src="js/jquery.js" type="text/javascript"></script>
        <script type="text/javascript" src="./js/jquery.form.js"></script>
        <script src="js/jquery-ui.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="js/jquery.cookie.js"></script>
        <!--script type="text/javascript" src="js/jquery.stickytableheaders.js"></script-->

        <!-- fancytree -->
        <link type="text/css" href="fancytree/skin-win8-n/ui.fancytree.css" rel="stylesheet"/>
        <script src="fancytree/jquery.fancytree-all.js" type="text/javascript"></script>
        <!-- /fancytree -->
        <link type="text/css" href="css/style.css" rel="stylesheet"/>
        <script type="text/javascript" src="js/jscript.js"></script>
        <title>AD FrontEnd on <?php echo "$site_name" ?></title>
    </head>
<body>
    <div class="top_div">
        <div class="header-text">
            Web-AD
        </div>
        <?php if ($_SESSION['admin'] !== true) { ?>
            <form name="autorization" method="post" action="index.php">
                <table class="header-table" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="left">DC=<?php echo $ad_host; ?></td>
                        <td>Логин:&nbsp;</td>
                        <td><input type="text" name="username" tabindex="1"/></td>
                        <td rowspan="2"><input type="submit" value="Вход" class="header-submit" tabindex="3"/></td>
                    </tr>
                    <tr>
                        <td align="left">DN=<?php echo $ad_domain; ?></td>
                        <td>Пароль:&nbsp;</td>
                        <td><input type="password" name="userpass" tabindex="2"/></td>
                    </tr>
                </table>
                <input type="hidden" name="act" value="autorization"/>
            </form>
        <?php
        } else {
            ?>
            <div title="Создать нового пользователя" onclick="create_user()" class="top_btn">
                +
            </div>
            <div title="Найти заблокированых пользователей" onclick="get_locked()" class="top_btn">
                FL
            </div>
            <div title="Выход" class="adm_panel" onclick="exit()">
                <?php echo $_SESSION['cur_username'] ?>
            </div>
        <?php } ?>
    </div>