<script type="text/javascript">
err_messages.add({
	'jname':'<translate id="83">Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%</translate>',
});
</script>
<dialog id="bhyve-rename" class="window-box new">
	<h1>
		<span class="new"><translate id="257">Rename virtual machine</translate></span>
	</h1>
	<h2><translate id="258">Renamed Virtual Machine Settings</translate></h2>
	<form class="win" method="post" id="bhyveRenameSettings" onsubmit="return false;">
		<div class="window-content">
			<p class="warning" style="width:400px;">
				<translate id="120">@rename_warning@</translate>
			</p>
			<p>
				<span class="field-name"><translate id="259">VM name</translate>:</span>
				<input type="text" name="jname" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-disable" />
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<translate id="216">Rename</translate>" class="new button ok-but" />
		<input type="button" value="<translate id="73">Cancel</translate>" class="button red cancel-but" />
	</div>
</dialog>
