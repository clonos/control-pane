<dialog id="bhyve-obtain" class="window-box">
	<h1><?php echo $this->translate('Create Virtual Machine from Library');?></h1>
	<h2><?php echo $this->translate('Virtual Machine Settings');?></h2>
	<form class="win" method="post" id="bhyveObtSettings">
		<div class="window-content">
			<p>
				<span class="field-name"><?php echo $this->translate('VM OS profile');?>:</span>
				<select name="vm_os_profile">
<?php echo $this->config->os_types_create('obtain'); ?>
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
				<span class="field-name"><?php echo $this->translate('IP address');?>:</span>
				<input type="text" name="ip4_addr" value="DHCP" pattern="^DHCP$|^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Gateway');?>:</span>
				<input type="text" name="gateway" value="10.0.0.1" pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Mask');?>:</span>
				<input type="text" name="mask" value="255.255.255.0" pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('VM Password');?>:</span>
				<input type="password" name="vm_password" value="cbsd" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20"></input> <small>— <?php echo $this->translate('default is');?>: «cbsd»</small>
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Authkey');?>:</span>
				<select name="vm_authkey">
<?php echo $this->config->authkeys_list(); ?>
				</select>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<?php echo $this->translate('Create');?>" class="button ok-but" />
		<input type="button" value="<?php echo $this->translate('Cancel');?>" class="button red cancel-but" />
	</div>
</dialog>
<?php
