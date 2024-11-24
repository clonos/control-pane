<script type="text/javascript">
err_messages.add({
	'jname':'<?php echo $this->translate("Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%");?>',
});
</script>
<dialog id="bhyve-rename" class="window-box new">
	<h1>
		<span class="new"><?php echo $this->translate('Rename virtual machine');?></span>
	</h1>
	<h2><?php echo $this->translate('Renamed Virtual Machine Settings');?></h2>
	<form class="win" method="post" id="bhyveRenameSettings" onsubmit="return false;">
		<div class="window-content">
			<p class="warning" style="width:400px;">
				<?php echo $this->translate('@rename_warning@'); ?>
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('VM name');?>:</span>
				<input type="text" name="jname" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-disable" />
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<?php echo $this->translate('Rename');?>" class="new button ok-but" />
		<input type="button" value="<?php echo $this->translate('Cancel');?>" class="button red cancel-but" />
	</div>
</dialog>
