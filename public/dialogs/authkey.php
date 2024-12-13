<script type="text/javascript">
err_messages.add({
	'keyname':'<translate id="83">Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%</translate>',
});
</script>
<dialog id="authkey" class="window-box">
	<h1><translate id="106">Create Authkey</translate></h1>
	<h2><translate id="1">Settings</translate></h2>
	<form class="win" method="post" id="authkeySettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><translate id="107">Authkey name</translate>:</span>
				<input type="text" name="keyname" value="" pattern="[\x20-\x21\x23-\x26\x28-\x7F]+" required="required" />
			</p>
			<p>
				<span class="field-name"><translate id="100">Authkey</translate>:</span>
				<textarea name="keysrc" rows="10"></textarea>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<translate id="72">Create</translate>" class="button ok-but" />
		<input type="button" value="<translate id="73">Cancel</translate>" class="button red cancel-but" />
	</div>
</dialog>
