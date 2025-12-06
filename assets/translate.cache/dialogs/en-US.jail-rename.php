<script type="text/javascript">
err_messages.add({
	'jname':'<span id="trlt-83">Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%</span>',
	'hostname':'<span id="trlt-84">This field can not be empty</span>',
	'ip':'<span id="trlt-85">Write correct ip address, e.g: 10.0.0.2</span>',
});
</script>
<dialog id="jail-rename" class="window-box new">
	<h1>
		<span class="new"><span id="trlt-214">Rename jail</span></span>
	</h1>
	<h2><span id="trlt-215">Renamed Jail Settings</span></h2>
	<form class="win" method="post" id="jailRenameSettings" onsubmit="return false;">
		<div class="window-content">
			<p class="warning" style="width:400px;">
				<span id="trlt-120">@rename_warning@</span>
			</p>
			<p>
				<span class="field-name"><span id="trlt-58">Jail name</span>:</span>
				<input type="text" name="jname" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-disable" />
			</p>
			<p>
				<span class="field-name"><span id="trlt-59">Hostname</span> (FQDN):</span>
				<input type="text" name="host_hostname" value="" required="required" />
			</p>
			<p>
				<span class="field-name"><span id="trlt-61">IP address</span>:</span>
				<input type="text" name="ip4_addr" value="DHCP" pattern="^DHCP$|^DHCP[vV]6$|^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="Rename" class="new button ok-but" />
		<input type="button" value="Cancel" class="button red cancel-but" />
	</div>
</dialog>
