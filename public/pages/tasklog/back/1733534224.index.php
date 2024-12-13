<?php
$clonos->useDialogs(array(
	'tasklog',
));
?>
<h1><translate>Task logs</translate></h1>

<p><span class="top-button icon-trash-empty id:flushlog"><translate>Flush log</translate></span></p>

<table class="tsimple" id="taskloglist" width="100%">
	<thead><tr>
		<th class="wdt-50 keyname"><translate>Task ID</translate></th>
		<th class="wdt-80"><translate>Log file</translate></th>
		<th class="txtleft"><translate>CMD</translate></th>
		<th class="wdt-130"><translate>Start time</translate></th>
		<th class="wdt-130"><translate>End time</translate></th>
		<th class="wdt-50"><translate>Status</translate></th>
		<th class="wdt-50"><translate>Error code</translate></th>
		<th class="wdt-50"><translate>Log size</translate></th>
	</tr></thead>
	<tbody></tbody>
</table>