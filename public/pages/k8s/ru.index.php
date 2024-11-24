<?php
$clonos->useDialogs(['k8s-new']);

/*
Модуль kubernetes использует в работе преднастроенный образ Linux, который не входит в базовую установку ClonOS.
Если вы хотите использовать модуль k8s, нажмите кнопку 'инициализация'  для скачивания и подготовки образа k8s к работе.
Подразумевается, что система имеет доступ в Internet. Объем образа который будет загружен приблизительно 4 GB.
*/

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