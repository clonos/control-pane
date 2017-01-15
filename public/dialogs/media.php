<script type="text/javascript">
err_messages={
	'netname':'<?php echo $this->translate("Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%");?>',
	'network':'<?php echo $this->translate("Write something");?>',
};
</script>
<dialog id="media" class="window-box">
	<h1><?php echo $this->translate('Add Storage Media');?></h1>
	<h2><?php echo $this->translate('Settings');?></h2>
	<form class="win" method="post" id="mediaSettings">
		<div class="window-content">
			<p>
				<span class="field-name"><?php echo $this->translate('Media name');?>:</span>
				<input type="text" name="medianame" value="" pattern="[\x20-\x21\x23-\x26\x28-\x7F]+" required="required" />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Path');?>:</span>
				<input type="text" name="mediapath" value="" pattern=".+" required="required" />
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<?php echo $this->translate('Create');?>" class="button ok-but" />
		<input type="button" value="<?php echo $this->translate('Cancel');?>" class="button red cancel-but" />
	</div>
</dialog>
