<?php
$clonos->useDialogs(array(
	'bases',
	'bases-repo',
));
?>
<h1><translate>FreeBSD bases:</translate></h1>

<p><span class="top-button icon-edit id:basescompile"><translate>Build from source code</translate></span>
	<span class="top-button icon-gift id:getrepo"><translate>Fetch from repository</translate></span></p>

<table class="tsimple" id="baseslist" width="100%">
	<thead>
		<tr>
			<th><translate>Node name</translate></th>
			<th><translate>Name</translate></th>
			<th class="wdt-80"><translate>Platform</translate></th>
			<th class="wdt-80"><translate>Arch</translate></th>
			<th class="wdt-80"><translate>TargetArch</translate></th>
			<th colspan="2" class="wdt-120"><translate>Ver</translate></th>
			<th class="wdt-80"><translate>Elf</translate></th>
			<th class="wdt-90"><translate>Action</translate></th>
			<th colspan="2" class="wdt-50"><translate>Action</translate></th>
			<th class="wdt-90">&nbsp;</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
