<script type="text/javascript">
err_messages.add({
	'netname':'<translate>Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%</translate>',
	'network':'<translate>Write something</translate>',
});
</script>
<dialog id="media" class="window-box">
	<h1><translate>Add Storage Media</translate></h1>
	<h2><translate>Settings</translate></h2>
	<form class="win" method="post" id="mediaSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><translate>Media name</translate>:</span>
				<input type="text" name="medianame" value="" pattern="[\x20-\x21\x23-\x26\x28-\x7F]+" required="required" />
			</p>
			<p>
				<span class="field-name"><translate>Path</translate>:</span>
				<input type="text" name="mediapath" value="" pattern=".+" required="required" />
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<translate>Create</translate>" class="button ok-but" />
		<input type="button" value="<translate>Cancel</translate>" class="button red cancel-but" />
	</div>
</dialog>
