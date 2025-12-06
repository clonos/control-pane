<?php
$this->useDialogs(array(
	'vnc-bhyve',
	'bhyve-new',
	'bhyve-obtain',
	'bhyve-clone',
	'bhyve-rename',
	'jail-settings-config-menu',
));
?>
<h1><translate id="12">Bhyve VMs</translate></h1>

<p>
	<span class="top-button icon-plus id:bhyve-new"><translate id="234">Create from ISO</translate></span>
	<span class="top-button icon-plus id:bhyve-obtain"><translate id="235">Cloud images</translate></span>
</p>

<table class="tsimple" id="bhyveslist" width="100%">
	<thead>
		<th class="wdt-120"><translate id="219">Node name</translate></th>
		<th class="txtleft"><translate id="236">VM</translate></th>
		<th class="wdt-120"><translate id="221">Usage</translate></th>
		<th class="txtcenter wdt-70"><translate id="237">RAM</translate></th>
		<th class="wdt-30"><translate id="238">CPU</translate></th>
		<th class="txtcenter wdt-100"><translate id="239">OS type</translate></th>
		<th class="txtcenter wdt-120"><translate id="222">Status</translate></th>
		<th colspan="4" class="wdt-100"><translate id="223">Action</translate></th>
		<th class="wdt-30"><translate id="224">VNC</translate></th>
		<th class="txtcenter wdt-50"><translate id="240">VNC port</translate></th>
	</thead>
	<tbody></tbody>
</table>
