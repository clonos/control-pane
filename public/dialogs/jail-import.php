<script type="text/javascript">
err_messages.add({
	'jname':'<translate id="83">Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%</translate>',
});
</script>
<dialog id="jail-import" class="window-box new">
	<h1>
		<span class="new"><translate id="205">Import jail</translate></span>
		<span class="edit"><translate id="56">Edit jail</translate></span>
	</h1>
	<h2><translate id="206">Jail Import</translate></h2>
	<form class="win" method="post" id="jailImport" onsubmit="return false;">
		<div class="window-content">
			<p>
				<!-- D&D Markup -->
				<div id="drag-and-drop-zone" class="uploader">
				  <div><translate id="207">Drag &amp; Drop Files Here</translate></div>
				  <div class="or"><translate id="208">-or-</translate></div>
				  <div class="browser">
				   <label>
					 <span><translate id="209">Click to open the file Browser</translate></span>
					 <input type="file" name="jimp_files[]" multiple="multiple" title='<translate id="210">Click to add Files</translate>'>
				   </label>
				  </div>
				</div>
				<div class="uploader-progress"></div>
				<!-- /D&D Markup -->
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<translate id="73">Cancel</translate>" class="button red cancel-but" />
	</div>
</dialog>
<script type="text/javascript">clonos.fileUploadPrepare();</script>