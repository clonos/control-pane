<div class="modal fade" id="jconfig" tabindex="-1" role="dialog" aria-labelledby="JailsConfig" aria-hidden="false">
	<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove-circle"></span></button>
			<h4 class="modal-title" id="JailsConfig"><span class="glyphicon"></span><?php echo $jname; ?> config:</h4>
		</div>
		<form name="fjconfig" id="fjconfig" class="navbar-form" role="create">
		<div class="row">
			<div class="col-md-6">
		    		<div class="input">
					<input input type="hidden" class="form-control" name="old_jname_rn" id="old_jname_rn" readonly value="<?php echo $jname; ?>"/>
		    		</div>
			</div>
		</div>
		
<!--$ip4_addr,$allow_mount,$devfs_ruleset,$interface,$baserw,$mount_ports,astart,vnet,ver,allow_sysvipc-->
		<div class="row">
			<div class="col-md-6">
				<label for="ip4_addr">ip4_addr</label>
			</div>
			<div class="col-md-6">
				<div class="input">
					<input type="text" class="form-control" placeholder="" name="new_hostname_rn" id="new_hostname_rn"/>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<label for="baserw">Base writable?</label>
			</div>
			<div class="col-md-6">
				<div class="input">
					<input type="checkbox" name="baserw" id="baserw" value="baserw"/>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<label for="mount_ports">Mount ports?</label>
			</div>
			<div class="col-md-6">
				<div class="input">
					<input type="checkbox" name="baserw" id="mount_ports" value="mount_ports"/>
				</div>
			</div>
		</div>

		
		</form>
		<div class="modal-footer">
			<input class="inp" type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="xajax_jconfig(xajax.getFormValues('fjconfig')); return false;" value="Save changes" />
		</div>
	</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
