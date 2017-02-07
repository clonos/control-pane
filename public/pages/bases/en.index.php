<?php
$clonos->useDialogs(array(
	'bases',
	'bases-repo',
));
?>
<h1>FreeBSD bases:</h1>

<p><span class="top-button icon-edit id:basescompile">Build for source code</span>
	<span class="top-button icon-gift id:getrepo">Fetch from repository</span></p>

<table class="tsimple" id="baseslist" width="100%">
	<thead>
		<tr>
			<th>Node name</th>
			<th>Name</th>
			<th class="wdt-80">Platform</th>
			<th class="wdt-80">Arch</th>
			<th class="wdt-80">TargetArch</th>
			<th colspan="2" class="wdt-120">Ver</th>
			<th class="wdt-80">Elf</th>
			<th class="wdt-90">Action</th>
			<th colspan="2" class="wdt-50">Action</th>
			<th class="wdt-90">&nbsp;</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>