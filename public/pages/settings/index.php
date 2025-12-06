<?php
$this->useDialogs(['settings-getupdate']);
?>

<h1><translate id="1">Settings</translate></h1>
<p><span class="top-button icon-plus id:settings-update"><translate id="351">Check for updates</translate></span>
	<span class="top-button icon-upload id:settings-getupdate hidden" id="but-getupdate"><translate id="352">Upgrade</translate></span></p>


<table class="tsimple" id="update_files" width="100%">
	<thead>
		<tr>
			<th class="txtleft"><translate id="353">Component</translate></th>
			<th class="txtcenter"><translate id="116">Version</translate></th>
			<th class="txtcenter"><translate id="354">Available</translate></th>
		</tr>
	</thead>
	<tbody></tbody>
</table>
