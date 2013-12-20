<?php
/*
Файл тела страницы
*/
if ($main_var != 'parol') exit;     // защита от запуска этого файла отдельнo

// Проверяем, произведен ли вход в систему
if ($_SESSION['admin'] === true) {
?>
<table id="main_table_tree" cellpadding="0" cellspacing="0" width="100%">
	<tr>
    	<td valign="top" id="tree">
        </td>
        <td valign="top" id="tree_objects" >
        </td>
    </tr>
    <tr height="20">
    	<td colspan="2" align="right">
        	<div id="page_info">
                    <?php
                    $E_TIME=round(((time()+microtime())-$S_TIME),8);        //Отсекаю время
                    echo "server: $E_TIME sec, ";
                    ?>
                    client: <span id="client_time"></span> sec
                    <br />
                    © Created by <a style="text-decoration:none" href="mailto:veron-33@yandex.ru">Veron</a>, 2013 <br />
        	</div>
        </td>
    </tr> 
</table>      
<?php          
}
else {
?>
<!-- Область для вывода сообщения об ошибке -->
<div class="error_div" id="error_div">Для продолжения работы Вам необходимо авторизоваться (без DN)!</div>
<script type="text/javascript">
document.getElementById('error_div').style.display = 'block';
</script>
<?php } ?>
