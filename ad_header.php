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

        <script src="//code.jquery.com/jquery-1.11.1.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="./js/jquery.form.js"></script>
        <script src="//code.jquery.com/ui/1.11.2/jquery-ui.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="js/jquery.cookie.js"></script>

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
            <table class="header-table2" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2">Вход выполнен:&nbsp;<?php echo $_SESSION['cur_username'] ?></td>
                </tr>
                <tr>
                    <td align="right"><a href="?exit">Выход</a></td>
                </tr>
            </table>
        <?php } ?>
    </div>
<?php if ($_SESSION['admin']) { ?>
    <div class="top_menu">
        <div id="create_user_btn" onclick="create_user()">Создать пользователя</div>
    </div>
<?php } ?>