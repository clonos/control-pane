<script type="text/javascript">
err_messages.add({
	'jname':'<?php echo $this->translate("Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%");?>',
	'host_hostname':'<?php echo $this->translate("This field can not be empty");?>',
	'ip':'<?php echo $this->translate("Write correct ip address, e.g: 10.0.0.2");?>',
});
</script>
<dialog id="jail-clone" class="window-box new">
	<h1>
		<span class="new"><?php echo $this->translate('Clone jail');?></span>
	</h1>
	<h2><?php echo $this->translate('Cloned Jail Settings');?></h2>
	<form class="win" method="post" id="jailCloneSettings" onsubmit="return false;">
		<div class="window-content">
			<p class="warning" style="width:400px;">
				<?php echo $this->translate('@clone_warning@'); ?>
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Jail name');?>:</span>
				<input type="text" name="jname" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{2,}" required="required" class="edit-disable" />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Hostname');?> (FQDN):</span>
				<input type="text" name="host_hostname" value="" required="required" />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('IP address');?>:</span>
				<input type="text" name="ip4_addr" value="DHCP" pattern="^DHCP$|^DHCP[vV]6$|^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<?php echo $this->translate('Clone');?>" class="new button ok-but" />
		<input type="button" value="<?php echo $this->translate('Cancel');?>" class="button red cancel-but" />
	</div>
</dialog>
