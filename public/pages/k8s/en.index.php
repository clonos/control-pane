<?php
/*
if(isset($clonos->uri_chunks[1]))
{
	include('helpers.php');
	return;
}
*/

/*
$clonos->useDialogs(array(
	'vnc',
	'jail-settings',
	'jail-settings-config-menu',
	'jail-import',
	'jail-clone',
	'jail-rename',
));
*/

?>
<h1>K8S clusters:</h1>
<!--
<p><span class="top-button icon-plus id:jail-settings">Создать контейнер</span>
<span class="top-button icon-upload id:jail-import">Импортировать</span></p>
-->

<table class="tsimple" id="k8slist" width="100%">
	<thead>
		<tr>
			<th class="elastic">name</th>
			<th class="txtcenter wdt-80">masters</th>
			<th class="txtcenter wdt-80">workers</th>
			<th class="txtleft">bhyves</th>
			<th colspan="4" class="txtcenter wdt-100">action</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>