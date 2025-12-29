<script type="text/javascript">
err_messages.add({});
</script>
<dialog id="media-upload" class="window-box">
	<h1><span id="trlt-306">Add Storage Media</span></h1>
	<h2><span id="trlt-307">Upload ISO</span></h2>
	<form class="win" method="post" id="mediaSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<!-- D&D Markup -->
				<div id="drag-and-drop-zone" class="uploader">
				  <div><span id="trlt-207">Drag &amp; Drop Files Here</span></div>
				  <div class="or"><span id="trlt-208">-or-</span></div>
				  <div class="browser">
				   <label>
					 <span><span id="trlt-209">Click to open the file Browser</span></span>
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
		<input type="button" value="Close" class="button red cancel-but" />
	</div>
</dialog>
<script type="text/javascript">clonos.fileUploadPrepare();</script>
