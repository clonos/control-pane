<script type="text/javascript">
err_messages.add({
	'jname':'Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%',
	'hostname':'This field can not be empty',
	'ip':'Write correct ip address, e.g: 10.0.0.2',
	'rootpass':'Password can not be less than 3 symbols',
	'rootpass1':'Please retype password correctly',
});
</script>
<dialog id="jail-settings" class="window-box new">
	<h1>
		<span class="new">Create jail</span>
		<span class="edit">Edit jail</span>
	</h1>
	<h2>Jail Settings</h2>
	<form class="win" method="post" id="jailSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name">Jail name:</span>
				<input type="text" name="jname" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-disable" />
			</p>
			<p>
				<span class="field-name">Hostname (FQDN):</span>
				<input type="text" name="host_hostname" value="" required="required" />
<!--
				<small class="astart-warn">— available on the jail is not running</small>
-->
			</p>
			<p>
				<span class="field-name">IP address:</span>
				<input type="text" name="ip4_addr" value="DHCP" pattern="^DHCP$|^DHCP[vV]6$|^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
			<p class="new">
				<span class="field-name">Root password:</span>
				<input type="password" name="user_pw_root" value="" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20"></input>
			</p>
			<p class="new">
				<span class="field-name">Root password (again):</span>
				<input type="password" name="user_pw_root_1" value="" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20"></input>
			</p>
			<p>
				<span class="field-name">Net Interface:</span>
				<input type="radio" name="interface" value="auto" id="rint0" checked="checked" class="inline"><label for="rint0">auto</label></radio>
				<input type="radio" name="interface" value="lo0" id="rint2" class="inline"><label for="rint2">lo0</label></radio>
			</p>
			<p>
				<span class="field-name">Parameters:</span>
				<input type="checkbox" name="baserw" id="bwritable-id" /><label for="bwritable-id"> Base writable?</label>
				<br />
				<input type="checkbox" name="mount_ports" id="mount-id" /><label for="mount-id"> Mount /usr/ports?</label>
				<br />
				<input type="checkbox" name="astart" id="astart-id" /><label for="astart-id"> Autostart jail at system startup</label>
				<br />
				<input type="checkbox" name="vnet" id="vnet-id" /><label for="vnet-id"> Virtual network stack (VIMAGE)</label>
			</p>
			<p class="new">
				<span class="field-name">Enabled services:</span>
				<input type="checkbox" name="serv-ftpd" value="ftpd" id="esrv0" class="inline"><label for="esrv0">ftpd</label></checkbox>
				<input type="checkbox" name="serv-sshd" value="sshd" id="esrv1" class="inline"><label for="esrv1">sshd</label></checkbox>
			</p>

		</div>
	</form>
	<div class="buttons">
		<input type="button" value="Create" class="new button ok-but" />
		<input type="button" value="Save" class="edit button ok-but" />
		<input type="button" value="Cancel" class="button red cancel-but" />
	</div>
</dialog>
<?php

/*
сложное правило для пароля: цифры, буквы маленькие и заглавные, плюс спецсимволы
^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[^\w\s]).{8,20}$
*/