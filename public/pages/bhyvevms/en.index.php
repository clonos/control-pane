<?php
$clonos->useDialogs(array(
	'vnc-bhyve',
	'bhyve-new',
	'bhyve-obtain',
	'bhyve-clone',
	'jail-settings-config-menu',
));
?>
<h1>Bhyve VMs</h1>

<p>
	<span class="top-button icon-plus id:bhyve-new">Create from ISO</span>
	<span class="top-button icon-plus id:bhyve-obtain">Obtain from lib</span>
</p>

<table class="tsimple" id="bhyveslist" width="100%">
	<thead>
		<th class="wdt-120">Node name</th>
		<th class="txtleft">VM</th>
		<th class="wdt-120">Usage</th>
		<th class="txtleft wdt-70">RAM</th>
		<th class="wdt-30">CPU</th>
		<th class="wdt-100">OS type</th>
		<th class="wdt-120">Status</th>
		<th colspan="4" class="wdt-100">Action</th>
		<th class="wdt-30">VNC</th>
		<th class="txtcenter wdt-50">VNC port</th>
	</thead>
	<tbody></tbody>
</table>