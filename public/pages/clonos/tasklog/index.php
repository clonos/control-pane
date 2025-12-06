<?php
$this->useDialogs(array(
	'tasklog',
));
?>
<h1><translate id="338">Task logs</translate></h1>

<p><span class="top-button icon-trash-empty id:flushlog"><translate id="339">Flush log</translate></span></p>

<table class="tsimple" id="taskloglist" width="100%">
	<thead><tr>
		<th class="wdt-50 keyname"><translate id="340">Task ID</translate></th>
		<th class="wdt-80"><translate id="341">Log file</translate></th>
		<th class="txtleft"><translate id="342">CMD</translate></th>
		<th class="wdt-130"><translate id="343">Start time</translate></th>
		<th class="wdt-130"><translate id="344">End time</translate></th>
		<th class="wdt-50"><translate id="222">Status</translate></th>
		<th class="wdt-50"><translate id="345">Error code</translate></th>
		<th class="wdt-50"><translate id="346">Log size</translate></th>
	</tr></thead>
	<tbody></tbody>
</table>