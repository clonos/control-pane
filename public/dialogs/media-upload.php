<script type="text/javascript">
err_messages.add({});
</script>
<dialog id="media-upload" class="window-box">
	<h1><translate id="306">Add Storage Media</translate></h1>
	<h2><translate id="307">Upload ISO</translate></h2>
	<form class="win" method="post" id="mediaSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<!-- D&D Markup -->
				<div id="drag-and-drop-zone" class="uploader">
				  <div><translate id="207">Drag &amp; Drop Files Here</translate></div>
				  <div class="or"><translate id="208">-or-</translate></div>
				  <div class="browser">
				   <label>
					 <span><translate id="209">Click to open the file Browser</translate></span>
					 <input type="file" name="files[]" multiple="multiple" title='Click to add Files'>
				   </label>
				  </div>
				</div>
				<div class="uploader-progress"></div>
				<!-- /D&D Markup -->
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<translate id="111">Close</translate>" class="button red cancel-but" />
	</div>
</dialog>
<script type="text/javascript">clonos.fileUploadPrepare();</script>
