<?php
$clonos->useDialogs(array(
	'vnc',
	'jail-settings',
	'jail-settings-config-menu',
));
?>
<h1>Контейнеры:</h1>

<p><span class="top-button icon-plus id:jail-settings">Создать контейнер</span></p>

<table class="tsimple" id="jailslist" width="100%">
	<thead>
		<tr>
			<th class="wdt-200 elastic">Имя сервера</th>
			<th class="txtleft">Контейнер</th>
			<th class="txtleft wdt-200">IP-адрес</th>
			<th class="wdt-120">Статус</th>
			<th colspan="4" class="wdt-100">Действия</th>
			<th class="wdt-30">VNC</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>