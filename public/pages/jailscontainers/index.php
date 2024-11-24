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
<h1>{translate:[Jail containers:]}</h1>

<p><span class="top-button icon-plus id:jail-settings">{translate:[Create jail]}</span>
<span class="top-button icon-upload id:jail-import">{translate:[Import]}</span></p>

<table class="tsimple" id="jailslist" width="100%">
	<thead>
		<tr>
			<th class="elastic">{translate:[Node name]}</th>
			<th class="txtleft">{translate:[Jail]}</th>
			<th class="wdt-120">{translate:[Usage]}</th>
			<th class="txtleft">{translate:[IP address]}</th>
			<th class="txtcenter wdt-120">{translate:[Status]}</th>
			<th colspan="4" class="txtcenter wdt-100">{translate:[Action]}</th>
			<th class="wdt-30">{translate:[VNC]}</th>
			<th class="txtcenter wdt-50" title="VNC port">{translate:[Port]}</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
