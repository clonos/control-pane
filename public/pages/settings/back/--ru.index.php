<?php
$clonos->useDialogs(['settings-getupdate']);
?>

<h1>Настройки</h1>
<p><span class="top-button icon-plus id:settings-update">Проверить обновления</span>
	<span class="top-button icon-upload id:settings-getupdate hidden" id="but-getupdate">Обновить</span></p>


<table class="tsimple" id="update_files" width="100%">
	<thead>
		<tr>
			<th class="txtleft">Компонент</th>
			<th class="txtcenter">Версия</th>
			<th class="txtcenter">Доступная версия</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
