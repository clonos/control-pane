<?php
if(isset($clonos->uri_chunks[1]))
{
	include('helpers.php');
	return;
}


$clonos->useDialogs(array(
	'vnc',
	'jail-settings',
	'jail-settings-config-menu',
	'jail-import',
	'jail-clone',
	'jail-rename',
));

?>
<h1>Jail containers:</h1>

<p><span class="top-button icon-plus id:jail-settings">Create jail</span>
<span class="top-button icon-upload id:jail-import">Import</span></p>

<table class="tsimple" id="jailslist" width="100%">
	<thead>
		<tr>
			<th class="elastic">Node name</th>
			<th class="txtleft">Jail</th>
			<th class="wdt-120">Usage</th>
			<th class="txtleft">IP address</th>
			<th class="txtcenter wdt-120">Status</th>
			<th colspan="4" class="txtcenter wdt-100">Action</th>
			<th class="wdt-30">VNC</th>
			<th class="txtcenter wdt-50" title="VNC port">Port</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
