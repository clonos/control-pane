<script type="text/javascript">
err_messages.add({
	'vm_name':'<translate id="83">Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%</translate>',
});
</script>
<dialog id="bhyve-clone" class="window-box new">
	<h1>
		<span class="new"><translate id="255">Clone Virtual Machine</translate></span>
	</h1>
	<h2><translate id="256">Cloned Virtual Machine Settings</translate></h2>
	<form class="win" method="post" id="bhyveCloneSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><translate id="98">Virtual Machine name</translate>:</span>
				<input type="text" name="vm_name" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{1,}" required="required" class="edit-disable" />
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<translate id="213">Clone</translate>" class="new button ok-but" />
		<input type="button" value="<translate id="73">Cancel</translate>" class="button red cancel-but" />
	</div>
</dialog>
