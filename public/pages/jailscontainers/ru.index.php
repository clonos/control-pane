<?php
if(isset($clonos->uri_chunks[1]))
{
	include('helpers.php');
	return;
}


$clonos->useDialogs(array(
	'vnc',
	'jail-settings',
	'jail-settings-config-menu',
	'jail-import',
	'jail-clone',
	'jail-rename',
));

?>
<h1>Контейнеры:</h1>

<p><span class="top-button icon-plus id:jail-settings">Создать контейнер</span>
<span class="top-button icon-upload id:jail-import">Импортировать</span></p>

<table class="tsimple" id="jailslist" width="100%">
	<thead>
		<tr>
			<th class="elastic">Имя сервера</th>
			<th class="txtleft">Контейнер</th>
			<th class="wdt-120">Нагрузка</th>
			<th class="txtleft">IP-адрес</th>
			<th class="txtcenter wdt-120">Статус</th>
			<th colspan="4" class="txtcenter wdt-100">Действия</th>
			<th class="wdt-30">VNC</th>
			<th class="txtcenter wdt-50" title="VNC порт">Порт</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>