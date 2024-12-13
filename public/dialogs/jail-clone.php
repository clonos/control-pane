<script type="text/javascript">
err_messages.add({
	'jname':'<translate id="83">Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%</translate>',
	'host_hostname':'<translate id="84">This field can not be empty</translate>',
	'ip':'<translate id="85">Write correct ip address, e.g: 10.0.0.2</translate>',
});
</script>
<dialog id="jail-clone" class="window-box new">
	<h1>
		<span class="new"><translate id="211">Clone jail</translate></span>
	</h1>
	<h2><translate id="212">Cloned Jail Settings</translate></h2>
	<form class="win" method="post" id="jailCloneSettings" onsubmit="return false;">
		<div class="window-content">
			<p class="warning" style="width:400px;">
				<translate id="119">@clone_warning@</translate>
			</p>
			<p>
				<span class="field-name"><translate id="58">Jail name</translate>:</span>
				<input type="text" name="jname" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-disable" />
			</p>
			<p>
				<span class="field-name"><translate id="59">Hostname</translate> (FQDN):</span>
				<input type="text" name="host_hostname" value="" required="required" />
			</p>
			<p>
				<span class="field-name"><translate id="61">IP address</translate>:</span>
				<input type="text" name="ip4_addr" value="DHCP" pattern="^DHCP$|^DHCP[vV]6$|^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<translate id="213">Clone</translate>" class="new button ok-but" />
		<input type="button" value="<translate id="73">Cancel</translate>" class="button red cancel-but" />
	</div>
</dialog>
