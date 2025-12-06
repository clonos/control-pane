<?php
$file=self::$realpath_page.'helpers.php';
if(isset($this->_uri_chunks[1]))
{
	if(file_exists($file))
	{
		include($file);
		return;
	}
}

$this->useDialogs([
	'vnc',
	'jail-settings',
	'jail-settings-config-menu',
	'jail-import',
	'jail-clone',
	'jail-rename',
]);

?>
<h1><span id="trlt-217">Jail containers:</span></h1>

<p><span class="top-button icon-plus id:jail-settings"><span id="trlt-55">Create jail</span></span>
<span class="top-button icon-upload id:jail-import"><span id="trlt-218">Import</span></span></p>

<table class="tsimple" id="jailslist" width="100%">
	<thead>
		<tr>
			<th class="elastic"><span id="trlt-219">Node name</span></th>
			<th class="txtleft"><span id="trlt-220">Jail</span></th>
			<th class="wdt-120"><span id="trlt-221">Usage</span></th>
			<th class="txtleft"><span id="trlt-61">IP address</span></th>
			<th class="txtcenter wdt-120"><span id="trlt-222">Status</span></th>
			<th colspan="4" class="txtcenter wdt-100"><span id="trlt-223">Action</span></th>
			<th class="wdt-30"><span id="trlt-224">VNC</span></th>
			<th class="txtcenter wdt-50" title="VNC port"><span id="trlt-225">Port</span></th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
