<script type="text/javascript">
err_messages.add({
	'name':'<?php echo $this->translate("CHANGE THIS TEXT!!! Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%");?>',
	'first_name':'<?php echo $this->translate("TYPE THIS TEXT!!!");?>',
	'last_name':'<?php echo $this->translate("TYPE THIS TEXT!!!");?>',
});
</script>
<dialog id="vm_packages-new" class="window-box new">
	<h1>
		<span class="new"><?php echo $this->translate('Add new template');?></span>
		<span class="edit"><?php echo $this->translate('Edit template');?></span>
	</h1>
	<h2><?php echo $this->translate('Template Settings');?></h2>
	<form class="win" method="post" id="templateSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><?php echo $this->translate('Name');?>:</span>
				<input type="text" name="name" value="" pattern=".{2,}" required="required" class="edit-enable" />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Description');?>:</span>
				<textarea name="description" rows="3"></textarea>
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('RAM Size');?>:</span>
				<input type="text" name="pkg_vm_ram" value="" pattern="^[0-9]+\s*(g|G|gb|GB|mb|MB|m|M)$" placeholder="1g" required="required" />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('HDD Size');?>:</span>
				<input type="text" name="pkg_vm_disk" value="" pattern="^[0-9]+\s*(g|G|gb|GB|mb|MB|m|M)$" placeholder="10g" required="required" />
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('CPUs Count');?>:</span>
				<span class="range">
					<input type="range" name="pkg_vm_cpus" class="vHorizon" min="1" max="16" value="1" style="margin:6px 0;" id="rngCpus" oninput="rngCpusShow.value=rngCpus.value" />
					<input type="text" disabled="disabled" id="rngCpusShow" value="1" />
				</span>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<?php echo $this->translate('Add');?>" class="new button ok-but" />
		<input type="button" value="<?php echo $this->translate('Save');?>" class="edit button ok-but" />
		<input type="button" value="<?php echo $this->translate('Cancel');?>" class="button red cancel-but" />
	</div>
</dialog>