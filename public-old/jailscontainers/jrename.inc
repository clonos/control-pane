<div class="modal fade" id="jrename" tabindex="-1" role="dialog" aria-labelledby="JailsRename" aria-hidden="false">
	<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove-circle"></span></button>
			<h4 class="modal-title" id="JailsRename"><span class="glyphicon"></span>Rename <?php echo $jname; ?> jail</h4>
		</div>
		<form name="fjrename" id="fjrename" class="navbar-form" role="create">
		<div class="row">
			<div class="col-md-6">
		    		<div class="input">
					<input input type="hidden" class="form-control" name="old_jname_rn" id="old_jname_rn" readonly value="<?php echo $jname; ?>"/>
		    		</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<label for="jname">New Jname:</label>
			</div>
			<div class="col-md-6">
				<div class="input">
					<input type="text" class="form-control" placeholder="FQDN" name="new_jname_rn" id="new_jname_rn"/>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-6">
				<label for="host_hostname">Host_hostname:</label>
			</div>
			<div class="col-md-6">
				<div class="input">
					<input type="text" class="form-control" placeholder="" name="new_hostname_rn" id="new_hostname_rn"/ value="<?php echo $host_hostname; ?>">
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-6">
				<label for="ip4_addr">ip4_addr (IP or 'DHCP'):</label>
			</div>
			<div class="col-md-6">
				<div class="input">
					<input type="text" class="form-control" placeholder="" name="new_ip4_addr_rn" id="new_ip4_addr_rn" value="<?php echo $ip4_addr; ?>"/>
				</div>
			</div>
		</div>
		
		</form>
		<div class="modal-footer">
			<input class="inp" type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="xajax_jrename(xajax.getFormValues('fjrename')); return false;" value="Save changes" />
		</div>
	</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
