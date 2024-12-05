<script type="text/javascript">
err_messages.add({
	'jname':'<?php echo $this->translate("Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%");?>',
});
</script>
<dialog id="jail-import" class="window-box new">
	<h1>
		<span class="new"><?php echo $this->translate('Import jail');?></span>
		<span class="edit"><?php echo $this->translate('Edit jail');?></span>
	</h1>
	<h2><?php echo $this->translate('Jail Import');?></h2>
	<form class="win" method="post" id="jailImport" onsubmit="return false;">
		<div class="window-content">
			<p>
				<!-- D&D Markup -->
				<div id="drag-and-drop-zone" class="uploader">
				  <div>Drag &amp; Drop Files Here</div>
				  <div class="or">-or-</div>
				  <div class="browser">
				   <label>
					 <span>Click to open the file Browser</span>
					 <input type="file" name="jimp_files[]" multiple="multiple" title='Click to add Files'>
				   </label>
				  </div>
				</div>
				<div class="uploader-progress"></div>
				<!-- /D&D Markup -->
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<?php echo $this->translate('Cancel');?>" class="button red cancel-but" />
	</div>
</dialog>
<script type="text/javascript">clonos.fileUploadPrepare();</script>