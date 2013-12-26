<?php
/*
Файл подвала страницы
*/
if ($main_var != 'parol') exit;     // защита от запуска этого файла отдельно
?>
<!-- Скрытый спан с сообщением ошибки -->
<span class="hidden" id="error_span"><?php echo (isset($error_text))?$error_text:""; ?></span>
<<<<<<< HEAD
<div id="dialog_div"></div>
=======
<div id="page_info1" class="hidden">
    <?php
$E_TIME=round(((time()+microtime())-$S_TIME),8);	//Отсекаю время
echo "server: $E_TIME sec, ";
?>
    client: <span id="client_time"></span> sec
    <br />
    © Created by <a style="text-decoration:none" href="mailto:veron-33@yandex.ru">Veron</a>, 2013 <br />
    <!--a href="http://jigsaw.w3.org/css-validator/check/referer">
    <img style="border:0;width:88px;height:31px"
        src="http://jigsaw.w3.org/css-validator/images/vcss-blue"
        alt="Правильный CSS!" />
	</a>
    <a href="http://validator.w3.org/check?uri=referer"><img style="border:0;width:88px;height:31px"
        src="http://www.w3.org/Icons/valid-xhtml10-blue"
        alt="Valid XHTML 1.0 Transitional" /> </a-->
</div>
>>>>>>> master
    <script type="text/javascript" >
	if ($('#error_span').html() != "") {
		$('#error_div').html($('#error_span').html());
		$('#error_div').css("display","block");
	}
	var now = new Date();
	$('#client_time').html((now - start)/1000);
</script>
</body></html>