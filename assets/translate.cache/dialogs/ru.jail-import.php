<script type="text/javascript">
err_messages.add({
	'jname':'<span id="trlt-83">Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%</span>',
});
</script>
<dialog id="jail-import" class="window-box new">
	<h1>
		<span class="new"><span id="trlt-205">Import jail</span></span>
		<span class="edit"><span id="trlt-56">Edit jail</span></span>
	</h1>
	<h2><span id="trlt-206">Jail Import</span></h2>
	<form class="win" method="post" id="jailImport" onsubmit="return false;">
		<div class="window-content">
			<p>
				<!-- D&D Markup -->
				<div id="drag-and-drop-zone" class="uploader">
				  <div><span id="trlt-207">Drag &amp; Drop Files Here</span></div>
				  <div class="or"><span id="trlt-208">-or-</span></div>
				  <div class="browser">
				   <label>
					 <span><span id="trlt-209">Click to open the file Browser</span></span>
					 <input type="file" name="jimp_files[]" multiple="multiple" Click to add Files>
				   </label>
				  </div>
				</div>
				<div class="uploader-progress"></div>
				<!-- /D&D Markup -->
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="Cancel" class="button red cancel-but" />
	</div>
</dialog>
<script type="text/javascript">clonos.fileUploadPrepare();</script>