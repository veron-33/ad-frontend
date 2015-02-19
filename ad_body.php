<?php
/*
Файл тела страницы
*/
if ($main_var != 'parol') exit;     // защита от запуска этого файла отдельнo

// Проверяем, произведен ли вход в систему
if ($_SESSION['admin'] === true) {
    ?>
    <div class="middle_div">
        <div id="left_div" style="height: 100%;overflow: auto">
            <div id="tree"></div>
        </div>
        <div id="right_div" style="height: 100%; overflow: auto">
            <table id="tree_objects">
                <colgroup>
                    <col style="width:26px; max-widt"></col>
                    <col width="800px"></col>
                    <col width="100px"></col>
                    <col width="100px"></col>
                </colgroup>
                <thead>
                <tr>
                    <th></th>
                    <th>Имя</th>
                    <th>Параметр1</th>
                    <th>Параметр2</th>
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
} else {
    ?>
    <!-- Область для вывода сообщения об ошибке -->
    <div class="error_div" id="error_div">Для продолжения работы Вам необходимо авторизоваться (без DN)!</div>
    <script type="text/javascript">
        $('#error_div').style.display = 'block';
    </script>
<?php } ?>
