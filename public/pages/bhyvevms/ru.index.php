<?php
$clonos->useDialogs(array(
	'vnc-bhyve',
	'bhyve-new',
	'bhyve-obtain',
	'jail-settings-config-menu',
));
?>
<h1>Виртуальные машины</h1>

<p>
	<span class="top-button icon-plus id:bhyve-new">Создать из ISO</span>
	<span class="top-button icon-plus id:bhyve-obtain">Из библиотеки</span>
</p>

<table class="tsimple" id="bhyveslist" width="100%">
	<thead>
		<th class="wdt-120">Имя сервера</th>
		<th class="txtleft">Виртуальная машина</th>
		<th class="txtleft wdt-70">RAM</th>
		<th class="wdt-30">CPU</th>
		<th class="wdt-100">Тип ОС</th>
		<th class="wdt-120">Статус</th>
		<th colspan="4" class="wdt-100">Действия</th>
		<th class="wdt-30">VNC</th>
	</thead>
	<tbody></tbody>
</table>