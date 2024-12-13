<dialog id="bhyve-obtain" class="window-box">
	<h1><translate id="96">Create Virtual Machine from Library</translate></h1>
	<h2><translate id="97">Virtual Machine Settings</translate></h2>
	<form class="win" method="post" id="bhyveObtSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><translate id="99">VM OS profile</translate>:</span>
				<select name="vm_os_profile" onchange="clonos.onChangeOsProfile(this,event);">
<?php echo $this->config->os_types_create('obtain'); ?>
				</select>
			</p>
			<p>
				<span class="field-name"><translate id="98">Virtual Machine name</translate>:</span>
				<input type="text" name="vm_name" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{1,}" required="required" />
			</p>
			<p>
				<span class="field-name"><translate id="243">VM template (cpu, ram, hdd)</translate>:</span>
				<select name="vm_packages" onchange="clonos.onChangePkgTemplate(this,event);">
<?php $vm_res=$this->config->vm_packages_list(); echo $vm_res['html']; ?>
				</select>
				<script type="text/javascript">clonos.vm_packages_obtain_min_id=<?php echo $vm_res['min_id']; ?>;</script>
			</p>
			<p>
				<span class="field-name"><translate id="101">VM CPUs</translate>:</span>
				<span class="range">
					<input type="range" name="vm_cpus" class="vHorizon" min="1" max="16" value="1" style="margin:6px 0;" id="rngCpus1" oninput="rngCpusShow1.value=rngCpus1.value" />
					<input type="text" disabled="disabled" id="rngCpusShow1" value="1" name="vm_cpus_show" />
				</span>
			</p>
			<p>
				<span class="field-name"><translate id="102">VM RAM</translate>:</span>
				<!-- <input type="text" name="vm_ram" value="" pattern="^[0-9]+(g|gb|mb|m)$" placeholder="1g" required="required" /> -->
				<span class="range">
					<input type="range" name="vm_ram" class="vHorizon" min="1" max="64" value="1" style="margin:6px 0;" id="rngRam1" oninput="rngRamShow1.value=rngRam1.value+'g'" />
					<input type="text" disabled="disabled" id="rngRamShow1" value="1" name="vm_ram_show" />
				</span>

			</p>
			<p>
				<span class="field-name"><translate id="103">VM Image size</translate>:</span>
				<!-- <input type="text" name="vm_size" value="" pattern="^[0-9]+(g|gb|mb|m|t|tb)$" placeholder="10g" required="required" /> -->
				<span class="range">
					<input type="range" name="vm_size" class="vHorizon" min="20" max="866" value="20" style="margin:6px 0;" id="rngImgsize1" oninput="rngImgsizeShow1.value=rngImgsize1.value+'g'" />
					<input type="text" disabled="disabled" id="rngImgsizeShow1" value="1" name="vm_imgsize_show" />
				</span>
			</p>
			<p>
				<span class="field-name"><translate id="61">IP address</translate>:</span>
				<input type="text" name="ip4_addr" value="DHCP" pattern="^DHCP$|^DHCP[vV]6$|^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
			<p>
				<span class="field-name"><translate id="250">Gateway</translate>:</span>
				<input type="text" name="gateway" value="10.0.0.1" pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
			<p>
				<span class="field-name"><translate id="251">Mask</translate>:</span>
				<input type="text" name="mask" value="255.255.255.0" pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
			<p>
				<span class="field-name"><translate id="105">VM Password</translate>:</span>
				<input type="password" name="vm_password" value="cbsd" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20"></input> <small>— <translate id="94">default is</translate>: «cbsd»</small>
			</p>
			<p>
				<span class="field-name"><translate id="252">USER Password</translate>:</span>
				<input type="password" name="user_password" value="" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20"></input> <small>— <translate id="253">user password (need correct)</translate></small>
			</p>
			<p>
				<span class="field-name"><translate id="100">Authkey</translate>:</span>
				<select name="vm_authkey">
<?php echo $this->config->authkeys_list(); ?>
				</select>
			</p>
			<p>
				<span class="field-name"><translate id="247">VNC Password</translate>:</span>
				<input type="password" name="vnc_password" value="cbsd" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20"></input> <small>— <translate id="254">use to log in VNC. Default is</translate>: cbsd</small>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="<translate id="72">Create</translate>" class="button ok-but" />
		<input type="button" value="<translate id="73">Cancel</translate>" class="button red cancel-but" />
	</div>
</dialog>
<?php
