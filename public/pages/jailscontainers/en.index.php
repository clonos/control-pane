<?php
$clonos->useDialogs(array(
	'vnc',
	'jail-settings',
	'jail-settings-config-menu',
	'jail-clone',
));
?>
<h1>Jail containers:</h1>

<p><span class="top-button icon-plus id:jail-settings">Create jail</span></p>

<table class="tsimple" id="jailslist" width="100%">
	<thead>
		<tr>
			<th class="wdt-200 elastic">Node name</th>
			<th class="txtleft">Jail</th>
			<th class="txtleft wdt-200">IP-address</th>
			<th class="wdt-120">Status</th>
			<th colspan="4" class="wdt-100">Action</th>
			<th class="wdt-30">VNC</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>