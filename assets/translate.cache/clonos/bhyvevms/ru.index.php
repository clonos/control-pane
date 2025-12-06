<?php
$this->useDialogs(array(
	'vnc-bhyve',
	'bhyve-new',
	'bhyve-obtain',
	'bhyve-clone',
	'bhyve-rename',
	'jail-settings-config-menu',
));
?>
<h1><span id="trlt-12">Bhyve VMs</span></h1>

<p>
	<span class="top-button icon-plus id:bhyve-new"><span id="trlt-234">Create from ISO</span></span>
	<span class="top-button icon-plus id:bhyve-obtain"><span id="trlt-235">Cloud images</span></span>
</p>

<table class="tsimple" id="bhyveslist" width="100%">
	<thead>
		<th class="wdt-120"><span id="trlt-219">Node name</span></th>
		<th class="txtleft"><span id="trlt-236">VM</span></th>
		<th class="wdt-120"><span id="trlt-221">Usage</span></th>
		<th class="txtcenter wdt-70"><span id="trlt-237">RAM</span></th>
		<th class="wdt-30"><span id="trlt-238">CPU</span></th>
		<th class="txtcenter wdt-100"><span id="trlt-239">OS type</span></th>
		<th class="txtcenter wdt-120"><span id="trlt-222">Status</span></th>
		<th colspan="4" class="wdt-100"><span id="trlt-223">Action</span></th>
		<th class="wdt-30"><span id="trlt-224">VNC</span></th>
		<th class="txtcenter wdt-50"><span id="trlt-240">VNC port</span></th>
	</thead>
	<tbody></tbody>
</table>
