<script type="text/javascript">
err_messages.add({
	'vm_name':'<?php echo $this->translate("Can not be empty. Name must begin with a letter / a-z / and not have any special symbols: -,.=%");?>',
});
</script>
<dialog id="k8s-new" class="window-box new">
	<h1>
		<span class="new"><?php echo $this->translate('Create Kubernetes');?></span>
	</h1>
	<h2><?php echo $this->translate('create master node and workers');?></h2>
	<form class="win" method="post" id="k8sNewSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><?php echo $this->translate('Master Nodes count');?>:</span>
				<span class="range">
					<input type="range" name="master_nodes" class="vHorizon" min="1" max="7" value="1" style="margin:6px 0;" id="rngMNodes" oninput="rngMNodesShow.value=rngMNodes.value">
					<input type="text" disabled="disabled" id="rngMNodesShow" value="1" name="master_cpus_count">
				</span>
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Master Nodes RAM size');?>:</span>
				<span class="range">
					<input type="range" name="master_ram" class="vHorizon" min="1" max="8" value="1" style="margin:6px 0;" id="rngMRam" oninput="rngMRamShow.value=rngMRam.value+'g'">
					<input type="text" disabled="disabled" id="rngMRamShow" value="1g" name="master_ram_size">
				</span>
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Master Node CPUs count');?>:</span>
				<span class="range">
					<input type="range" name="master_cpus" class="vHorizon" min="1" max="16" value="1" style="margin:6px 0;" id="rngMCpus" oninput="rngMCpusShow.value=rngMCpus.value">
					<input type="text" disabled="disabled" id="rngMCpusShow" value="1" name="master_cpus_count">
				</span>
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Master Node Image size');?>:</span>
				<span class="range">
					<input type="range" name="master_img" class="vHorizon" min="10" max="40" value="10" style="margin:6px 0;" id="rngMImg" oninput="rngMImgShow.value=rngMImg.value+'gb'">
					<input type="text" disabled="disabled" id="rngMImgShow" value="10gb" name="master_img_size">
				</span>
			</p>
			
			<p>
				<span class="field-name"><?php echo $this->translate('Worker Nodes count');?>:</span>
				<span class="range">
					<input type="range" name="worker_nodes" class="vHorizon" min="1" max="8" value="1" style="margin:6px 0;" id="rngWNodes" oninput="rngWNodesShow.value=rngWNodes.value">
					<input type="text" disabled="disabled" id="rngWNodesShow" value="1" name="worker_nodes_count">
				</span>
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Worker Nodes RAM size');?>:</span>
				<span class="range">
					<input type="range" name="worker_ram" class="vHorizon" min="1" max="8" value="1" style="margin:6px 0;" id="rngWRam" oninput="rngWRamShow.value=rngWRam.value+'g'">
					<input type="text" disabled="disabled" id="rngWRamShow" value="1g" name="worker_ram_size">
				</span>
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Worker Nodes CPUs count');?>:</span>
				<span class="range">
					<input type="range" name="worker_cpus" class="vHorizon" min="1" max="16" value="1" style="margin:6px 0;" id="rngWCpus" oninput="rngWCpusShow.value=rngWCpus.value">
					<input type="text" disabled="disabled" id="rngWCpusShow" value="1" name="worker_cpus_count">
				</span>
			</p>
			<p>
				<span class="field-name"><?php echo $this->translate('Worker Nodes Image size');?>:</span>
				<span class="range">
					<input type="range" name="worker_img" class="vHorizon" min="10" max="40" value="10" style="margin:6px 0;" id="rngWImg" oninput="rngWImgShow.value=rngWImg.value+'gb'">
					<input type="text" disabled="disabled" id="rngWImgShow" value="10gb" name="worker_img_size">
				</span>
			</p>
			
			<p>
				<span class="field-name"><?php echo $this->translate('Parameters');?>:</span>
				<input type="checkbox" name="pv_enable" id="pvenable-id"><label for="pvenable-id"> <?php echo $this->translate('PV on or off');?></label>
				<br>
				<input type="checkbox" name="kubelet_master" id="kubmaster-id"><label for="kubmaster-id"> <?php echo $this->translate('Master and Worker is a same thing');?></label>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<?php echo $this->translate('Create');?>" class="new button ok-but" />
		<input type="button" value="<?php echo $this->translate('Cancel');?>" class="button red cancel-but" />
	</div>
</dialog>
