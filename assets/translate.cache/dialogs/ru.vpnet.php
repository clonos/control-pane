<script type="text/javascript">
err_messages.add({
	'netname':'<span id="trlt-83">Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%</span>',
	'network':'<span id="trlt-85">Write correct ip address, e.g: 10.0.0.2</span>',
});
</script>
<dialog id="vpnet" class="window-box">
	<h1><span id="trlt-299">Create Network</span></h1>
	<h2><span id="trlt-1">Общая статистика облака:</span></h2>
	<form class="win" method="post" id="vpnetSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><span id="trlt-300">Network name</span>:</span>
				<input type="text" name="netname" value="" pattern="[\x20-\x21\x23-\x26\x28-\x7F]+" required="required" />
			</p>
			<p>
				<span class="field-name"><span id="trlt-301">Network</span>:</span>
				<input type="text" name="network" value="" pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="Create" class="button ok-but" />
		<input type="button" value="Cancel" class="button red cancel-but" />
	</div>
</dialog>
