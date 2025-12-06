<?php
$clonos->useDialogs(array(
	'vnc-bhyve',
	'bhyve-new',
	'bhyve-obtain',
	'bhyve-clone',
	'bhyve-rename',
	'jail-settings-config-menu',
));
?>
<h1><translate>Bhyve VMs</translate></h1>

<p>
	<span class="top-button icon-plus id:bhyve-new"><translate>Create from ISO</translate></span>
	<span class="top-button icon-plus id:bhyve-obtain"><translate>Cloud images</translate></span>
</p>

<table class="tsimple" id="bhyveslist" width="100%">
	<thead>
		<th class="wdt-120"><translate>Node name</translate></th>
		<th class="txtleft"><translate>VM</translate></th>
		<th class="wdt-120"><translate>Usage</translate></th>
		<th class="txtcenter wdt-70"><translate>RAM</translate></th>
		<th class="wdt-30"><translate>CPU</translate></th>
		<th class="txtcenter wdt-100"><translate>OS type</translate></th>
		<th class="txtcenter wdt-120"><translate>Status</translate></th>
		<th colspan="4" class="wdt-100"><translate>Action</translate></th>
		<th class="wdt-30"><translate>VNC</translate></th>
		<th class="txtcenter wdt-50"><translate>VNC port</translate></th>
	</thead>
	<tbody></tbody>
</table>
