<dialog id="bhyve-obtain" class="window-box">
	<h1><span id="trlt-96">Создание из библиотеки</span></h1>
	<h2><span id="trlt-97">Настройки</span></h2>
	<form class="win" method="post" id="bhyveObtSettings" onsubmit="return false;">
		<div class="window-content">
			<p>
				<span class="field-name"><span id="trlt-99">Профиль операционной системы</span>:</span>
				<select name="vm_os_profile" onchange="clonos.onChangeOsProfile(this,event);">
<?php echo $this->config->os_types_create('obtain'); ?>
				</select>
			</p>
			<p>
				<span class="field-name"><span id="trlt-98">Имя виртуальной машины</span>:</span>
				<input type="text" name="vm_name" value="" pattern="[^0-9]{1}[a-zA-Z0-9]{1,}" required="required" />
			</p>
			<p>
				<span class="field-name"><span id="trlt-243">VM template (cpu, ram, hdd)</span>:</span>
				<select name="vm_packages" onchange="clonos.onChangePkgTemplate(this,event);">
<?php $vm_res=$this->config->vm_packages_list(); echo $vm_res['html']; ?>
				</select>
				<script type="text/javascript">clonos.vm_packages_obtain_min_id=<?php echo $vm_res['min_id']; ?>;</script>
			</p>
			<p>
				<span class="field-name"><span id="trlt-101">Количество виртуальных ядер</span>:</span>
				<span class="range">
					<input type="range" name="vm_cpus" class="vHorizon" min="1" max="16" value="1" style="margin:6px 0;" id="rngCpus1" oninput="rngCpusShow1.value=rngCpus1.value" />
					<input type="text" disabled="disabled" id="rngCpusShow1" value="1" name="vm_cpus_show" />
				</span>
			</p>
			<p>
				<span class="field-name"><span id="trlt-102">Объём памяти</span>:</span>
				<!-- <input type="text" name="vm_ram" value="" pattern="^[0-9]+(g|gb|mb|m)$" placeholder="1g" required="required" /> -->
				<span class="range">
					<input type="range" name="vm_ram" class="vHorizon" min="1" max="64" value="1" style="margin:6px 0;" id="rngRam1" oninput="rngRamShow1.value=rngRam1.value+'g'" />
					<input type="text" disabled="disabled" id="rngRamShow1" value="1" name="vm_ram_show" />
				</span>

			</p>
			<p>
				<span class="field-name"><span id="trlt-103">Объём виртуального диска</span>:</span>
				<!-- <input type="text" name="vm_size" value="" pattern="^[0-9]+(g|gb|mb|m|t|tb)$" placeholder="10g" required="required" /> -->
				<span class="range">
					<input type="range" name="vm_size" class="vHorizon" min="20" max="866" value="20" style="margin:6px 0;" id="rngImgsize1" oninput="rngImgsizeShow1.value=rngImgsize1.value+'g'" />
					<input type="text" disabled="disabled" id="rngImgsizeShow1" value="1" name="vm_imgsize_show" />
				</span>
			</p>
			<p>
				<span class="field-name"><span id="trlt-61">IP-адрес</span>:</span>
				<input type="text" name="ip4_addr" value="DHCP" pattern="^DHCP$|^DHCP[vV]6$|^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
			<p>
				<span class="field-name"><span id="trlt-250">Gateway</span>:</span>
				<input type="text" name="gateway" value="10.0.0.1" pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
			<p>
				<span class="field-name"><span id="trlt-251">Mask</span>:</span>
				<input type="text" name="mask" value="255.255.255.0" pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}(\/[\d]{1,3})?$" required="required" />
			</p>
			<p>
				<span class="field-name"><span id="trlt-105">Пароль администратора</span>:</span>
				<input type="password" name="vm_password" value="cbsd" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20"></input> <small>— <span id="trlt-94">по-умолчанию</span>: «cbsd»</small>
			</p>
			<p>
				<span class="field-name"><span id="trlt-252">USER Password</span>:</span>
				<input type="password" name="user_password" value="" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20"></input> <small>— <span id="trlt-253">user password (need correct)</span></small>
			</p>
			<p>
				<span class="field-name"><span id="trlt-100">SSH ключ</span>:</span>
				<select name="vm_authkey">
<?php echo $this->config->authkeys_list(); ?>
				</select>
			</p>
			<p>
				<span class="field-name"><span id="trlt-247">VNC Password</span>:</span>
				<input type="password" name="vnc_password" value="cbsd" placeholder="3-20 symbols" pattern=".{3,20}" maxlength="20"></input> <small>— <span id="trlt-254">use to log in VNC. Default is</span>: cbsd</small>
			</p>
		</div>
	</form>
	<div class="buttons">
		<input type="button" value="Создать" class="button ok-but" />
		<input type="button" value="Отменить" class="button red cancel-but" />
	</div>
</dialog>
<?php
