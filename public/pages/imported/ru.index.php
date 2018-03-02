<?php
$clonos->useDialogs(array(
	'jail-import',
	'image-import',
//	'jail-settings-config-menu',
));
?>
<h1>Импортированные образы:</h1>

<span class="top-button icon-upload id:jail-import">Импортировать</span></p>

<table class="tsimple" id="impslist" width="100%">
	<thead>
		<td class="keyname">Имя файла</td>
		<!-- <td class="txtleft">Путь</td> -->
		<td class="txtleft wdt-200">Тип файла</td>
		<td class="wdt-80">Действия</td>
	</thead>
	<tbody></tbody>
</table>