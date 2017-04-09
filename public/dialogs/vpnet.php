<script type="text/javascript">
err_messages.add({
	'netname':'<?php echo $this->translate("Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%");?>',
	'network':'<?php echo $this->translate("Write correct ip address, e.g: 10.0.0.2");?>',
});
</script>
<dialog id="vpnet" class="window-box">
	<h1><?php echo $this->translate('Create Network');?></h1>
	<h2><?php echo $this->translate('Settings');?></h2>
	<form class="win" method="post" id="vpnetSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><?php echo $this->translate('Network name');?>:</span>
				<input type="text" name="netname" value="" pattern="[\x20-\x21\x23-\x26\x28-\x7F]+" required="required" />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Network');?>:</span>
				<input type="text" name="network" value="" pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<?php echo $this->translate('Create');?>" class="button ok-but" />
		<input type="button" value="<?php echo $this->translate('Cancel');?>" class="button red cancel-but" />
	</div>
</dialog>
