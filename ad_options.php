<div>
<a href="?p=tree" title="Отобразить дерево каталогов AD">&bull; Просмотр дерева</a><br />
<a href="?p=cr_user" title="Создать нового пользователя в AD">&bull; Создать пользователя</a><br />
<a href="?p=btable" title="Перейти к таблице с ботами">&bull; Редактор ботов</a><br />
<a href="?p=blist" title="Перейти к черному списку пользователей">&bull; Черный список IP</a><br />
<a href="?p=report" title="Перейти к странице отчетов">&bull; Отчеты</a><br />

<hr width="90%" />
Вывести в диапазоне дат:</div>
<form method="post" name="date1" action="?p=mtable">
    <input type="hidden" name="no_bots" value="<?php echo $no_bots?'1':'0'; ?>" />
    <table class="range-table" align="center">
        <tr>
            <td>Начало:&nbsp;</td>
            <td><input type="text" name="date_from" value="<?php echo $start_date; ?>" maxlength="10" size="11" onclick="event.cancelBubble=true;this.select();lcs(this)" onfocus="this.select();lcs(this)" /></td>
        </tr>
        <tr>
            <td>Конец:&nbsp;</td>
            <td><input type="text" name="date_to" value="<?php echo $end_date; ?>" maxlength="10" size="11" onclick="event.cancelBubble=true;this.select();lcs(this)" onfocus="this.select();lcs(this)" /></td>
            <td><input type="submit" name="act" value="OK" /></td>
        </tr>
    </table>
</form>
<center>
    <form method="post" name="date2" action="?p=mtable">
        <input type="hidden" name="act" value="dates" />
        <input type="hidden" name="no_bots" value="<?php echo ($no_bots) ? '1' : '0'; ?>" />
        <input type="submit" name="for_today" value="За сегодня" /><br />
        <input type="submit" name="by_month" value="С начала месяца" /><br />
        <input type="checkbox" name="bots" onclick="change_bots(1)" <?php if ($no_bots) echo "checked='checked'"; ?> />
        без поисковыx ботов<br />        
    </form>
    <hr width="90%" />
    <br />
    <form method="post" action="?p=mtable">
        <input type="hidden" name="act" value="del_myself" />
        <input type="submit" value="Удалить себя из списка" />
    </form>
</center>