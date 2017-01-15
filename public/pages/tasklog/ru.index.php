<?php
$clonos->useDialogs(array(
	'tasklog',
));
?>
<h1>Логи задач</h1>

<!--p>
	<span class="top-button icon-plus id:vpnet">Добавить подсеть</span>
</p-->

<table class="tsimple" id="taskloglist" width="100%">
	<thead><tr>
		<th class="wdt-50 keyname">ID задачи</th>
		<th class="wdt-80">Файл лога</th>
		<th class="txtleft">Команда</th>
		<th class="wdt-130">Время старта</th>
		<th class="wdt-130">Время завершения</th>
		<th class="wdt-50">Статус</th>
		<th class="wdt-50">Код ошибки</th>
		<th class="wdt-50">Размер лога</th>
	</tr></thead>
	<tbody></tbody>
</table>