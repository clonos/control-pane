<script type="text/javascript">
err_messages={
	'keyname':'<?php echo $this->translate("Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%");?>',
};
</script>
<dialog id="authkey" class="window-box">
	<h1><?php echo $this->translate('Create Authkey');?></h1>
	<h2><?php echo $this->translate('Settings');?></h2>
	<form class="win" method="post" id="authkeySettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><?php echo $this->translate('Authkey name');?>:</span>
				<input type="text" name="keyname" value="" pattern="[\x20-\x21\x23-\x26\x28-\x7F]+" required="required" />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Authkey');?>:</span>
				<textarea name="keysrc" rows="10"></textarea>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<?php echo $this->translate('Create');?>" class="button ok-but" />
		<input type="button" value="<?php echo $this->translate('Cancel');?>" class="button red cancel-but" />
	</div>
</dialog>
