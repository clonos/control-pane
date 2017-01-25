<script type="text/javascript">
err_messages={
	'vm_name':'<?php echo $this->translate("Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%");?>',
	'vm_size':'You need type «g» char after numbers',
	'vm_ram':'You need type «g» char after numbers',
};
</script>
<dialog id="bhyve-new" class="window-box">
	<h1><?php echo $this->translate('Create Virtual Machine');?></h1>
	<h2><?php echo $this->translate('Virtual Machine Settings');?></h2>
	<form class="win" method="post" id="bhyveSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><?php echo $this->translate('VM OS profile');?>:</span>
				<select name="vm_os_profile">
<?php echo $this->config->os_types_create(); ?>
				</select>
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Virtual Machine name');?>:</span>
				<input type="text" name="vm_name" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{1,}" required="required" />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('VM Image size');?>:</span>
				<input type="text" name="vm_size" value="" pattern="^[0-9]+g$" placeholder="10g" required="required" />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('VM CPUs');?>:</span>
				<input type="text" name="vm_cpus" value="" pattern="[0-9]+" placeholder="1" required="required" />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('VM RAM');?>:</span>
				<input type="text" name="vm_ram" value="" pattern="^[0-9]+g$" placeholder="1g" required="required" />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('VNC PORT');?>:</span>
				<input type="text" name="vnc_port" value="" placeholder="0" />
			</p>
<!--			<p>
				<span class="field-name"><?php echo $this->translate('CD-ROM ISO');?>:</span>
				<select name="cd-rom">
					<option value="profile">profile</option>
				</select>
			</p>
-->			<p>
				<span class="field-name"><?php echo $this->translate('Net Interface');?>:</span>
				<input type="radio" name="interface" value="auto" id="rint0" checked="checked" class="inline"><label for="rint0">auto</label></radio>
				<input type="radio" name="interface" value="lo0" id="rint2" class="inline"><label for="rint2">lo0</label></radio>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<?php echo $this->translate('Create');?>" class="button ok-but" />
		<input type="button" value="<?php echo $this->translate('Cancel');?>" class="button red cancel-but" />
	</div>
</dialog>
<?php
