<?php
/*
Файл подвала страницы
*/
if ($main_var != 'parol') exit;     // защита от запуска этого файла отдельно
?>
<!-- Скрытый спан с сообщением ошибки -->
<span class="hidden" id="error_span"><?php echo (isset($error_text))?$error_text:""; ?></span>
<div id="dialog_div"></div>
    <script type="text/javascript" >
	if ($('#error_span').html() != "") {
		$('#error_div').html($('#error_span').html());
		$('#error_div').css("display","block");
	}
	var now = new Date();
	$('#client_time').html((now - start)/1000);
</script>
</body></html>