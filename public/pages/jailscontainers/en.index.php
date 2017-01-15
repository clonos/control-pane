<?php
$clonos->useDialogs(array(
	'vnc',
	'jail-settings',
	'jail-settings-config-menu',
));
?>
<h1>Jail containers:</h1>

<p><span class="top-button icon-plus id:jail-settings">Create jail</span></p>

<table class="tsimple" id="jailslist" width="100%">
	<thead>
		<th>Node</th>
		<th class="txtleft">Jail name</th>
		<th class="txtleft">IP Address</th>
		<th>Status</th>
		<th colspan="4">Action</th>
		<th>VNC</th>
	</thead>
	<tbody></tbody>
</table>