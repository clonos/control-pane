<?php
$clonos->useDialogs(array(
	'tasklog',
));
?>
<h1>Task logs</h1>

<p><span class="top-button icon-trash-empty id:flushlog">Flush log</span></p>

<table class="tsimple" id="taskloglist" width="100%">
	<thead><tr>
		<th class="wdt-50 keyname">Task ID</th>
		<th class="wdt-80">Log file</th>
		<th class="txtleft">CMD</th>
		<th class="wdt-130">Start time</th>
		<th class="wdt-130">End time</th>
		<th class="wdt-50">Status</th>
		<th class="wdt-50">Error code</th>
		<th class="wdt-50">Log size</th>
	</tr></thead>
	<tbody></tbody>
</table>