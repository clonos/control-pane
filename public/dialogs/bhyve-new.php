<script type="text/javascript">
err_messages.add({
	'vm_name':'<?php echo $this->translate("Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%");?>',
	'vm_size':'You need type «g» char after numbers',
	'vm_ram':'You need type «g» char after numbers',
});
</script>
<dialog id="bhyve-new" class="window-box">
	<h1>
		<span class="new"><?php echo $this->translate('Create Virtual Machine');?></span>
		<span class="edit"><?php echo $this->translate('Edit Virtual Machine');?></span>
	</h1>
	<h2><?php echo $this->translate('Virtual Machine Settings');?></h2>
	<form class="win" method="post" id="bhyveSettings" onsubmit="return false;">
		<div class="window-content">
			<p class="new">
				<span class="field-name"><?php echo $this->translate('VM OS profile');?>:</span>
				<select name="vm_os_profile">
<?php echo $this->config->os_types_create(); ?>
				</select>
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Virtual Machine name');?>:</span>
				<input type="text" name="vm_name" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{1,}" required="required" class="edit-disable" />
			</p>
			<p class="new">
				<span class="field-name"><?php echo $this->translate('VM template (cpu, ram, hdd)');?>:</span>
				<select name="vm_packages" onchange="clonos.onChangePkgTemplate(this,event);">
<?php $vm_res=$this->config->vm_packages_list(); echo $vm_res['html']; ?>
				</select>
				<script type="text/javascript">clonos.vm_packages_new_min_id=<?php echo $vm_res['min_id']; ?>;</script>
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('VM CPUs');?>:</span>
				<span class="range">
					<input type="range" name="vm_cpus" class="vHorizon" min="1" max="16" value="1" style="margin:6px 0;" id="rngCpus" oninput="rngCpusShow.value=rngCpus.value" />
					<input type="text" disabled="disabled" id="rngCpusShow" value="1" name="vm_cpus_show" />
					<!-- input type="text" name="vm_cpus" value="" pattern="[0-9]+" placeholder="1" required="required" / -->
				</span>
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('VM RAM');?>:</span>
				<input type="text" name="vm_ram" value="" pattern="^[0-9]+\s*(g|gb|mb|m|t|tb)$" placeholder="1g" required="required" />
			</p>
			<p class="new">
				<span class="field-name"><?php echo $this->translate('VM Image size');?>:</span>
				<input type="text" name="vm_size" value="" pattern="^[0-9]+(g|gb|t|tb)$" placeholder="10g" required="required" class="edit-disable" />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Attached boot ISO image');?>:</span>
				<select name="vm_iso_image">
					<option value="-2"></option>
					<option value="-1" selected>Profile default ISO</option>
<?php echo $this->media_iso_list_html(); ?>
				</select>
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('VNC IP address');?>:</span>
				<input type="radio" name="bhyve_vnc_tcp_bind" value="127.0.0.1" id="vncip0" checked="checked" class="inline"><label for="vncip0">127.0.0.1</label></radio>
				<input type="radio" name="bhyve_vnc_tcp_bind" value="0.0.0.0" id="vncip1" class="inline"><label for="vncip1">0.0.0.0</label></radio>
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('VNC PORT');?>:</span>
				<input type="text" name="vm_vnc_port" value="" placeholder="0" maxlength="5" />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('VNC Password');?>:</span>
				<input type="password" name="vm_vnc_password" value="" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20"></input> <small>— <?php echo $this->translate('use to log in VNC console');?></small>
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
		<input type="button" value="<?php echo $this->translate('Create');?>" class="new button ok-but" />
		<input type="button" value="<?php echo $this->translate('Save');?>" class="edit button ok-but" />
		<input type="button" value="<?php echo $this->translate('Cancel');?>" class="button red cancel-but" />
	</div>
</dialog>
