<?php
/*
if(isset($clonos->uri_chunks[1]))
{
	include('helpers.php');
	return;
}
*/

$clonos->useDialogs(array(
	'k8s-new',
));

?>
<h1>K8S кластеры:</h1>
<p><span class="top-button icon-plus id:k8s-new">Создать Kubernetes</span></p>

<table class="tsimple" id="k8slist" width="100%">
	<thead>
		<tr>
			<th class="wdt-70">ID кластера</th>
			<th class="elastic txtleft wdt-150">Имя кластера</th>
			<th class="txtcenter wdt-80">Кол-во master нод</th>
			<th class="txtcenter wdt-80">Кол-во worker нод</th>
			<th class="txtleft">Список виртуальных машин</th>
			<th colspan="4" class="txtcenter wdt-100">Действия</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>