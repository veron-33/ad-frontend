<?php
if ($main_var != 'parol') exit;     // защита от запуска этого файла отдельно
//header("Access-Control-Allow-Origin: *");
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
    <script type="text/javascript" src="js/jquery.ui-contextmenu.js"></script>
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
    <?php if ($_SESSION['admin'] == true) { ?>
        <!-- Top buttons begin -->
        <div title="Создать нового пользователя" onclick="create_user()" class="top_btn">+</div>
        <div title="Поиск пользователя" onclick="find_user()" class="top_btn">F</div>
        <div title="Найти заблокированых пользователей" onclick="get_locked()" class="top_btn">FL</div>
        <!-- Top buttons end -->
        <div title="Выход" class="adm_panel" onclick="exit()">
            <?php echo $_SESSION['cur_username'] ?>
        </div>
    <?php } ?>
</div>


<?php
// Проверяем, произведен ли вход в систему
if ($_SESSION['admin'] === true) {
    ?>
    <div class="middle_div">
        <div id="left_div">
            <div id="tree" style="height: 100%;overflow: auto"></div>
        </div>
        <div id="right_div" style="height: 100%; overflow: auto">
            <table id="tree_objects">
                <colgroup>
                    <col style="width:26px; max-width: 26px;">
                    <col>
                    <col width="150px">
                </colgroup>
                <thead>
                <tr>
                    <th style="width: 26px; max-width: 26px;"></th>
                    <th>Имя</th>
                    <th width="150px">Тип</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
    <div class="bottom_div">
        <div class="page_info">
            <?php
            $E_TIME = round(((time() + microtime()) - $S_TIME), 8);        //Отсекаю время
            echo "server: $E_TIME sec, ";
            ?>
            client: <span id="client_time"></span> sec
            © Created by <a style="text-decoration:none" href="mailto:veron-33@yandex.ru">Veron</a>, 2013-2015
        </div>
    </div>
<?php
} else {?>
    <div class="auth_div">
        <form action="index.php" method="post" name="authorization">
            <div class="inputu">
                <input type="text" name="login" tabindex="1"/>
            </div>
            <div class="inputp">
                <input type="password" name="pass" tabindex="2"/>
                <input class="auth_submit" type="submit" value="→" tabindex="3"/>
            </div>
            <div>
                <select name="dc" id="dc">
                    <?php
                    foreach ($dcs as $host) {
                        echo "<option value='".$host."''>".$host."</option>";
                    }
                    ?>
                </select>
            </div>
                <input type="hidden" name="act" value="auth"/>
        </form>
    </div>

<?php }

if (isset($error_text) && $error_text != "") {
    ?>
    <div class="error_div"><?php echo  $error_text; ?></div>
<?php
}
?>
<div id="dialog_div"></div>
<script type="text/javascript" >
    var now = new Date();
    $('#client_time').html((now - start)/1000);
</script>
</body></html>