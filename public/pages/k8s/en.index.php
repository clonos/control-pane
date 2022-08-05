<?php
$clonos->useDialogs(['k8s-new']);
?>
<h1>K8S Сlusters:</h1>
<p><span class="top-button icon-plus id:k8s-new">Создать Kubernetes</span></p>

<table class="tsimple" id="k8slist" width="100%">
	<thead>
		<tr>
			<th class="wdt-70">Cluster ID</th>
			<th class="elastic txtleft wdt-150">Cluster Name</th>
			<th class="txtcenter wdt-80">Masters Count</th>
			<th class="txtcenter wdt-80">Workers Count</th>
			<th class="txtleft">VM list</th>
			<th colspan="4" class="txtcenter wdt-100">Actions</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
