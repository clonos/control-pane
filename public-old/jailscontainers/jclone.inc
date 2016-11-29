<div class="modal fade" id="jclone" tabindex="-1" role="dialog" aria-labelledby="JailsClone" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
        	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove-circle"></span></button>
                <h4 class="modal-title" id="JailsClone"><span class="glyphicon"></span>Clone <?php echo $jname; ?> jail</h4>
            </div>
            <form name="fjclone" id="fjclone" class="navbar-form" role="create">
            <div class="row">
		<div class="col-md-6">
		    <div class="input">
			<input type="hidden" class="form-control" name="old_jname" id="old_jname" readonly value="<?php echo $jname; ?>"/>
		    </div>
		</div>
	    </div>
            <div class="row">
		<div class="col-md-6">
		    <label for="jname">New Jname</label>
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="text" class="form-control" placeholder="Cloned Jail Name" name="new_jname" id="new_jname"/>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <label for="fqdn">FQDN:</label>
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="text" class="form-control" placeholder="example.com.ua" name="host_hostname_cl" id="host_hostname_cl" value="<?php echo $host_hostname; ?>"/>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <label for="ip4_addr">Ipv4_addr (IP or 'DHCP'):</label>
		</div>
		<div class="col-md-6">
		    <div class="input-group">
<!--			<input type="text" class="form-control" placeholder="DHCP or IPv4" name="ip4_addr_cl" id="ip4_addr_cl" onkeypress="check_IP_MASK('ip4_addr_cl', 'ip4_addr_correct_cl')" onfocus="check_IP_MASK('ip4_addr_cl', 'ip4_addr_correct_cl')" onkeyup="check_IP_MASK('ip4_addr_cl', 'ip4_addr_correct_cl')"/>-->
			<input type="text" class="form-control" placeholder="DHCP or IPv4" name="ip4_addr_cl" id="ip4_addr_cl" onkeypress="check_IP_MASK('ip4_addr_cl','ip4_addr_correct_cl')" onkeyup="check_IP_MASK('ip4_addr_cl', 'ip4_addr_correct_cl')" value="<?php echo $ip4_addr; ?>"/>
			<span class="input-group-addon"><div class="label label-info" id="ip4_addr_correct_cl"><span class="glyphicon glyphicon-arrow-left"></span></div></span>
		    </div>
		</div>
	    </div>
	    </form>
	    <div class="modal-footer">
		<input class="inp" type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="xajax_jclone(xajax.getFormValues('fjclone')); return false;" value="Clone Jail" />
	    </div>
    	</div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
