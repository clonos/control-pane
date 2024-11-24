<script type="text/javascript">
err_messages.add({});
</script>
<dialog id="media-upload" class="window-box">
	<h1><?php echo $this->translate('Add Storage Media');?></h1>
	<h2><?php echo $this->translate('Upload ISO');?></h2>
	<form class="win" method="post" id="mediaSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<!-- D&D Markup -->
				<div id="drag-and-drop-zone" class="uploader">
				  <div>Drag &amp; Drop Files Here</div>
				  <div class="or">-or-</div>
				  <div class="browser">
				   <label>
					 <span>Click to open the file Browser</span>
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
		<input type="button" value="<?php echo $this->translate('Close');?>" class="button red cancel-but" />
	</div>
</dialog>
<script type="text/javascript">clonos.fileUploadPrepare();</script>
