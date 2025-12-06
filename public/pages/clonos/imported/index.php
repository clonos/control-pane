<?php
$this->useDialogs(array(
	'jail-import',
	'image-import',
//	'jail-settings-config-menu',
));
?>
<h1><translate id="308">Imported images:</translate></h1>

<span class="top-button icon-upload id:jail-import"><translate id="218">Import</translate></span></p>

<table class="tsimple" id="impslist" width="100%">
	<thead>
		<td class="keyname"><translate id="309">Image name</translate></td>
		<td class="txtcenter wdt-120 impsize"><translate id="310">Size</translate></td>
		<td class="txtleft wdt-150"><translate id="311">Type</translate></td>
		<th class="txtcenter wdt-120"><translate id="222">Status</translate></th>
		<td class="wdt-80"><translate id="223">Action</translate></td>
	</thead>
	<tbody></tbody>
</table>