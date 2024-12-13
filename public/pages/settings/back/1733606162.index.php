<?php
$clonos->useDialogs(['settings-getupdate']);
?>

<h1><translate>Settings</translate></h1>
<p><span class="top-button icon-plus id:settings-update"><translate>Check for updates</translate></span>
	<span class="top-button icon-upload id:settings-getupdate hidden" id="but-getupdate"><translate>Upgrade</translate></span></p>


<table class="tsimple" id="update_files" width="100%">
	<thead>
		<tr>
			<th class="txtleft"><translate>Component</translate></th>
			<th class="txtcenter"><translate>Version</translate></th>
			<th class="txtcenter"><translate>Available</translate></th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
