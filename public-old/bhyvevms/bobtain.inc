<script type="text/javascript">
	function bobtain_ge(i){if(typeof(i)=='object')return i;return document.getElementById(i);};
	function bobtain_ae(o,e,f){o=bobtain_ge(o);if(!o||!f||!e)return;if(o.addEventListener)o.addEventListener(e,f,false);else o.attachEvent('on'+e,f);};
	var bobtain_list = [
		{
			title: 'FreeBSD',
			value: 'freebsd',
			def: 1,
			ver: [
				{
					title: '11.0-RELEASE',
					value: 'FreeBSD-x64-11.0',
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
		}
	];
	function bobtain_ver(v){var s,o,i,j;if(!(s=bobtain_ge('bobtain_selectVER')))return;while(s.length)s.remove(0);for(i in bobtain_list)if(bobtain_list[i].value==v)for(j in bobtain_list[i].ver){o=document.createElement('option');o.text=bobtain_list[i].ver[j].title;o.value=bobtain_list[i].ver[j].value;o.selected=bobtain_list[i].ver[j].def?true:false;s.appendChild(o);};};
	bobtain_ae(window,'load',function(){var s=bobtain_ge('bobtain_selectOS');if(s){var o,i;for(i in bobtain_list){o=document.createElement('option');o.text=bobtain_list[i].title;o.value=bobtain_list[i].value;o.selected=bobtain_list[i].def?true:false;s.appendChild(o);if(bobtain_list[i].def)bobtain_ver(o.value);};bobtain_ae(s,'change',function(){bobtain_ver(this.value);});};});
</script>

<div class="modal fade" id="bobtain" tabindex="-1" role="dialog" aria-labelledby="BhyveObtain" aria-hidden="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove-circle"></span></button>
				<h4 class="modal-title" id="BhyveObtain"><span class="glyphicon"></span>Bhyve obtain</h4>
			</div>

			<form name="fbobtain" id="fbobtain" class="navbar-form" role="create">

			<div class="row">
				<div class="col-md-6">
					<label for="bobtain_vm_os_type">VM OS Type</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<select name="bobtain_vm_os_type" id="bobtain_selectOS"><!--
							<option selected value="linux">Linux</option>
							<option value="freebsd">FreeBSD</option>
						--></select>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<label for="bobtain_vm_os_profile">VM OS Profile</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<select name="bobtain_vm_os_profile" id="bobtain_selectVER"><!--
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
					<label for="ip4_addr">Ipv4_addr</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<input type="text" class="form-control" placeholder="DHCP" name="ip4_addr" value="DHCP" id="ip4_addr"/>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<label for="gw">Gateway</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<input type="text" class="form-control" placeholder="1g" name="gw" value="10.0.0.1" id="gw"/>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<label for="mask">Mask</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<input type="text" class="form-control" placeholder="255.255.255.0" value="255.255.255.0" name="mask" id="mask"/>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<label for="vm_pw">VM Password (default is: 'cbsd'):</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<input type="password" class="form-control" placeholder="1g" name="vm_pw" value="cbsd" id="vm_pw"/>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<label for="vm_authkey">Authkey:</label>
				</div>
				<div class="col-md-6">
					<div class="input">
						<select name="vm_authkey">
						<?php
						echo $authkey_list;
						?>
						</select>
					</div>
				</div>
			</div>

			</form>

			<div class="modal-footer">
				<input class="inp" type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="xajax_bobtain(xajax.getFormValues('fbobtain')); return false;" value="Obtain VM" />
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
