<script type="text/javascript">
err_messages.add({
	'vm_name':'<?php echo $this->translate("Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%");?>',
});
</script>
<dialog id="bhyve-clone" class="window-box new">
	<h1>
		<span class="new"><?php echo $this->translate('Clone Virtual Machine');?></span>
	</h1>
	<h2><?php echo $this->translate('Cloned Virtual Machine Settings');?></h2>
	<form class="win" method="post" id="bhyveCloneSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><?php echo $this->translate('Virtual Machine name');?>:</span>
				<input type="text" name="vm_name" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{1,}" required="required" class="edit-disable" />
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<?php echo $this->translate('Clone');?>" class="new button ok-but" />
		<input type="button" value="<?php echo $this->translate('Cancel');?>" class="button red cancel-but" />
	</div>
</dialog>
