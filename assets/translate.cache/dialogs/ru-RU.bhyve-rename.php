<script type="text/javascript">
err_messages.add({
	'jname':'<span id="trlt-83">Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%</span>',
});
</script>
<dialog id="bhyve-rename" class="window-box new">
	<h1>
		<span class="new"><span id="trlt-257">Rename virtual machine</span></span>
	</h1>
	<h2><span id="trlt-258">Renamed Virtual Machine Settings</span></h2>
	<form class="win" method="post" id="bhyveRenameSettings" onsubmit="return false;">
		<div class="window-content">
			<p class="warning" style="width:400px;">
				<span id="trlt-120">@rename_warning@</span>
			</p>
			<p>
				<span class="field-name"><span id="trlt-259">VM name</span>:</span>
				<input type="text" name="jname" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-disable" />
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="Rename" class="new button ok-but" />
		<input type="button" value="Cancel" class="button red cancel-but" />
	</div>
</dialog>
