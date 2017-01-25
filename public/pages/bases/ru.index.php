<?php
$clonos->useDialogs(array(
	'bases',
	'bases-repo',
));
?>
<h1>Базы FreeBSD:</h1>

<p><span class="top-button icon-edit id:basescompile">Собрать из исходных кодов</span>
	<span class="top-button icon-gift id:getrepo">Получить из репозитория</span></p>

<table class="tsimple" id="baseslist" width="100%">
	<thead>
		<tr>
			<th>Имя сервера</th>
			<th>Имя</th>
			<th class="wdt-80">Платформа</th>
			<th class="wdt-80">Архитект.</th>
			<th class="wdt-80">TargetArch</th>
			<th colspan="2" class="wdt-120">Версия</th>
			<th class="wdt-80">Elf</th>
			<th class="wdt-90">Action</th>
			<th colspan="2" class="wdt-50">Действия</th>
			<th class="wdt-90">&nbsp;</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>