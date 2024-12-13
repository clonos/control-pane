<script type="text/javascript">
err_messages.add({
	'netname':'<translate id="83">Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%</translate>',
	'network':'<translate id="85">Write correct ip address, e.g: 10.0.0.2</translate>',
});
</script>
<dialog id="vpnet" class="window-box">
	<h1><translate id="299">Create Network</translate></h1>
	<h2><translate id="1">Settings</translate></h2>
	<form class="win" method="post" id="vpnetSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><translate id="300">Network name</translate>:</span>
				<input type="text" name="netname" value="" pattern="[\x20-\x21\x23-\x26\x28-\x7F]+" required="required" />
			</p>
			<p>
				<span class="field-name"><translate id="301">Network</translate>:</span>
				<input type="text" name="network" value="" pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<translate id="72">Create</translate>" class="button ok-but" />
		<input type="button" value="<translate id="73">Cancel</translate>" class="button red cancel-but" />
	</div>
</dialog>
