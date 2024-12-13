<?php
if(isset($clonos->uri_chunks[1])){
	include('helpers.php');
	return;
}

$clonos->useDialogs([
	'vnc',
	'jail-settings',
	'jail-settings-config-menu',
	'jail-import',
	'jail-clone',
	'jail-rename',
]);

?>
<h1><translate id="217">Jail containers:</translate></h1>

<p><span class="top-button icon-plus id:jail-settings"><translate id="55">Create jail</translate></span>
<span class="top-button icon-upload id:jail-import"><translate id="218">Import</translate></span></p>

<table class="tsimple" id="jailslist" width="100%">
	<thead>
		<tr>
			<th class="elastic"><translate id="219">Node name</translate></th>
			<th class="txtleft"><translate id="220">Jail</translate></th>
			<th class="wdt-120"><translate id="221">Usage</translate></th>
			<th class="txtleft"><translate id="61">IP address</translate></th>
			<th class="txtcenter wdt-120"><translate id="222">Status</translate></th>
			<th colspan="4" class="txtcenter wdt-100"><translate id="223">Action</translate></th>
			<th class="wdt-30"><translate id="224">VNC</translate></th>
			<th class="txtcenter wdt-50" title="VNC port"><translate id="225">Port</translate></th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
