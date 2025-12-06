<script type="text/javascript">
err_messages.add({
	'keyname':'<span id="trlt-83">Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%</span>',
});
</script>
<dialog id="authkey" class="window-box">
	<h1><span id="trlt-106">Create Authkey</span></h1>
	<h2><span id="trlt-1">Общая статистика облака:</span></h2>
	<form class="win" method="post" id="authkeySettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><span id="trlt-107">Authkey name</span>:</span>
				<input type="text" name="keyname" value="" pattern="[\x20-\x21\x23-\x26\x28-\x7F]+" required="required" />
			</p>
			<p>
				<span class="field-name"><span id="trlt-100">Authkey</span>:</span>
				<textarea name="keysrc" rows="10"></textarea>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="Create" class="button ok-but" />
		<input type="button" value="Cancel" class="button red cancel-but" />
	</div>
</dialog>
