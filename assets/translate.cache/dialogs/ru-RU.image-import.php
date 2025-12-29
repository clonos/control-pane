<script type="text/javascript">
err_messages.add({
	'jname':'<span id="trlt-83">Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%</span>',
	'hostname':'<span id="trlt-84">This field can not be empty</span>',
	'ip':'<span id="trlt-85">Write correct ip address, e.g: 10.0.0.2</span>',
});
</script>
<dialog id="image-import" class="window-box new">
	<h1>
		<span class="new"><span id="trlt-312">Image Import</span></span>
		<!-- <span class="edit"><span id="trlt-56">Edit jail</span></span> -->
	</h1>
	<h2><span id="trlt-1">Settings</span></h2>
	<form class="win" method="post" id="imageImportSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><span id="trlt-313">New name</span>:</span>
				<input type="text" name="jname" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-disable" />
				<div class="inp_comment" id="name_comment"></div>
			</p>
			<p>
				<span class="field-name"><span id="trlt-59">Hostname</span> (FQDN):</span>
				<input type="text" name="host_hostname" value="" required="required" />
			</p>
			<p>
				<span class="field-name"><span id="trlt-61">IP address</span>:</span>
				<input type="text" name="ip4_addr" value="DHCP" pattern="^DHCP$|^DHCP[vV]6$|^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
			<input type="hidden" name="file_id" value="" />
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="Import" class="new button ok-but" />
		<input type="button" value="Cancel" class="button red cancel-but" />
	</div>
</dialog>
