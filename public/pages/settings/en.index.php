<?php
$clonos->useDialogs(['settings-getupdate']);
?>

<h1>Settings</h1>
<p><span class="top-button icon-plus id:settings-update">Check for updates</span>
	<span class="top-button icon-upload id:settings-getupdate hidden" id="but-getupdate">Upgrade</span></p>


<table class="tsimple" id="update_files" width="100%">
	<thead>
		<tr>
			<th class="txtleft">Component</th>
			<th class="txtcenter">Version</th>
			<th class="txtcenter">Available</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
