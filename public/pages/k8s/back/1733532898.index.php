<?php
$clonos->useDialogs(['k8s-new']);
?>
<h1><translate>K8S Сlusters:</translate></h1>
<p><span class="top-button icon-plus id:k8s-new"><translate>Create Kubernetes</translate></span></p>

<table class="tsimple" id="k8slist" width="100%">
	<thead>
		<tr>
			<th class="wdt-70"><translate>Cluster ID</translate></th>
			<th class="elastic txtleft wdt-150"><translate>Cluster Name</translate></th>
			<th class="txtcenter wdt-80"><translate>Masters Count</translate></th>
			<th class="txtcenter wdt-80"><translate>Workers Count</translate></th>
			<th class="txtleft"><translate>VM list</translate></th>
			<th colspan="4" class="txtcenter wdt-100"><translate>Actions</translate></th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
