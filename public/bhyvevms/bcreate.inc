<script type="text/javascript">
	function bcreate_ge(i){if(typeof(i)=='object')return i;return document.getElementById(i);};
	function bcreate_ae(o,e,f){o=bcreate_ge(o);if(!o||!f||!e)return;if(o.addEventListener)o.addEventListener(e,f,false);else o.attachEvent('on'+e,f);};
	var bcreate_list = [
		{
			title: 'FreeBSD',
			value: 'freebsd',
			def: 1,
			ver: [
				{
					title: '11.0-RELEASE',
					value: 'FreeBSD-x64-11.0',
					def: 1
				},
				{
					title: 'pfSense 2.4.0-DEVELOP',
					value: 'pfSense-2-LATEST-amd64',
					def: 0
				},
				{
					title: 'OPNsense-16.7',
					value: 'OPNsense-16-RELEASE-amd64',
					def: 0
				}
			]
		},
		{
			title: 'Linux Arch',
			value: 'linux arch',
			def: 0,
			ver: [
				{
					title: 'Arch 2016',
					value: 'ArchLinux-x86-2016',
					def: 1
				}
			]
		},
		{
			title: 'Linux CentOS',
			value: 'linux centos',
			def: 0,
			ver: [
				{
					title: 'CentOS 7',
					value: 'CentOS-7-x86_64',
					def: 1
				}
			]
		},
		{
			title: 'Linux Debian',
			value: 'linux debian',
			def: 0,
			ver: [
				{
					title: 'Debian 7',
					value: 'Debian-x86-7',
					def: 0
				},
				{
					title: 'Debian 8',
					value: 'Debian-x86-8',
					def: 1
				}
			]
		},
		{
			title: 'Linux OpenSuSE',
			value: 'linux opensuse',
			def: 0,
			ver: [
				{
					title: 'Open Suse 42',
					value: 'opensuse-x86-42',
					def: 1
				}
			]
		},
		{
			title: 'Linux Ubuntu',
			value: 'linux ubuntu',
			def: 0,
			ver: [
				{
					title: 'Ubuntu 16.04',
					value: 'ubuntuserver-x86-16.04',
					def: 1
				}
			]
		},
		{
			title: 'Microsoft Windows',
			value: 'windows windows',
			def: 0,
			ver: [
				{
					title: 'Windows 10',
					value: '10_86x_64x',
					def: 1
				}
				
			]
		}
	];
	function bcreate_ver(v){var s,o,i,j;if(!(s=bcreate_ge('bcreate_selectVER')))return;while(s.length)s.remove(0);for(i in bcreate_list)if(bcreate_list[i].value==v)for(j in bcreate_list[i].ver){o=document.createElement('option');o.text=bcreate_list[i].ver[j].title;o.value=bcreate_list[i].ver[j].value;o.selected=bcreate_list[i].ver[j].def?true:false;s.appendChild(o);};};
	bcreate_ae(window,'load',function(){var s=bcreate_ge('bcreate_selectOS');if(s){var o,i;for(i in bcreate_list){o=document.createElement('option');o.text=bcreate_list[i].title;o.value=bcreate_list[i].value;o.selected=bcreate_list[i].def?true:false;s.appendChild(o);if(bcreate_list[i].def)bcreate_ver(o.value);};bcreate_ae(s,'change',function(){bcreate_ver(this.value);});};});
</script>


<div class="modal fade" id="bcreate" tabindex="-1" role="dialog" aria-labelledby="BhyveCreate" aria-hidden="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove-circle"></span></button>
				<h4 class="modal-title" id="BhyveCreate"><span class="glyphicon"></span>Bhyve create</h4>
			</div>

			<form name="fbcreate" id="fbcreate" class="navbar-form" role="create">

			<div class="row">
				<div class="col-md-6">
					<label for="bcreate_vm_os_type">VM OS Type</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<select name="bcreate_vm_os_type" id="bcreate_selectOS"><!--
							<option selected value="linux">Linux</option>
							<option value="freebsd">FreeBSD</option>
						--></select>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<label for="bcreate_vm_os_profile">VM OS Profile</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<select name="bcreate_vm_os_profile" id="bcreate_selectVER"><!--
							<option selected value="ubuntuserver-x86-16.04">Ubuntu Server 16.04</option>
							<option value="FreeBSD-x64-11.0">FreeBSD 11.0-RELEASE</option>
						--></select>
					</div>
				</div>
			</div>


			<div class="row">
				<div class="col-md-6">
					<label for="jname">VM name</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<input type="text" class="form-control" placeholder="VM name" name="jname" id="jname"/>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<label for="fqdn">Image size</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<input type="text" class="form-control" placeholder="10g" name="vm_size" value="10g" id="vm_size"/>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<label for="vm_cpus">VM CPUs</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<input type="text" class="form-control" placeholder="1" name="vm_cpus" value="1" id="vm_cpus"/>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<label for="vm_ram">VM RAM</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<input type="text" class="form-control" placeholder="1g" name="vm_ram" value="1g" id="vm_ram"/>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<label for="vnc_port">VNC PORT</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<input type="text" class="form-control" placeholder="1g" name="vnc_port_" value="0" id="vnc_port"/>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<label for="cd_rom">CD-ROM ISO:</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<select name="cd_rom">
						<?php
						echo $os_iso_list;
						?>
						</select>
					</div>
				</div>
			</div>

			</form>

			<div class="modal-footer">
				<input class="inp" type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="xajax_bcreate(xajax.getFormValues('fbcreate')); return false;" value="Create VM" />
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
