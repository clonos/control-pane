<script type="text/javascript">
function checkservises(item) {
var item_servis_value = document.getElementById(item).value;
var item_servises_value = document.getElementById('sysrc_enable').value;

    if (document.getElementById(item).checked == true) {
        var tmp1 = item_servises_value.concat(" ",item_servis_value);
        document.getElementById('sysrc_enable').value = tmp1.trimMiddle();
    } else {
        var tmp2 = item_servises_value.replace (new RegExp (item_servis_value, 'g'), '');
        document.getElementById('sysrc_enable').value = tmp2.trimMiddle();
    }
}
</script>
<div class="modal fade" id="jcreate" tabindex="-1" role="dialog" aria-labelledby="JailsCreate" aria-hidden="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
        	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove-circle"></span></button>
                <h4 class="modal-title" id="JailsCreate"><span class="glyphicon"></span>Jails create</h4>
            </div>
            <form name="fjcreate" id="fjcreate" class="navbar-form" role="create">
            <div class="row">
		<div class="col-md-6">
		    <label for="jname">Jname</label>
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="text" class="form-control" placeholder="Jail Name" name="jname" id="jname" value="<?php echo $freejname; ?>"/>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <label for="fqdn">Fqdn</label>
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="text" class="form-control" placeholder="example.my.domain" name="host_hostname" id="host_hostname" value="<?php echo $freejname; ?>.my.domain"/>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <label for="ip4_addr">Ipv4_addr</label>
		</div>
		<div class="col-md-6">
		    <div class="input-group">
<!--			<input type="text" class="form-control" placeholder="DHCP or IPv4" name="ip4_addr" id="ip4_addr" onkeypress="check_IP_MASK('ip4_addr', 'ip4_addr_correct')" onfocus="check_IP_MASK('ip4_addr', 'ip4_addr_correct')" onkeyup="check_IP_MASK('ip4_addr', 'ip4_addr_correct')"/>-->
			<input type="text" class="form-control" placeholder="DHCP or IPv4" name="ip4_addr" id="ip4_addr" value="DHCP" onkeypress="check_IP_MASK('ip4_addr','ip4_addr_correct')" onkeyup="check_IP_MASK('ip4_addr', 'ip4_addr_correct')"/>
			<span class="input-group-addon"><div class="label label-info" id="ip4_addr_correct"><span class="glyphicon glyphicon-arrow-left"></span></div></span>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <label for="interface">Interface</label>
		</div>
		<div class="col-md-6">
		    <div class="input">
			<select name="interface" id="interface" class="form-control" OnChange="">
                	    <option value="auto" selected="selected">auto</option>
                	    <option value="em0">em0</option>
                	    <option value="lo0">lo0</option>
            		</select>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <!-- <label for="mount_devfs">mount_devfs</label> -->
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="hidden" name="mount_devfs" id="mount_devfs" value="1"/>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
	    	    <!-- <label for="arch">arch</label> -->
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="hidden" name="arch" id="arch" value="native"/>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <!-- <label for="mkhostfile">mkhostfile</label> -->
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="hidden" name="mkhostfile" id="mkhostfile" value="1"/>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <!-- <label for="arch">devfs_ruleset</label> -->
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="hidden" name="devfs_ruleset" id="devfs_ruleset" value="4"/>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
	    	    <!-- <label for="ver">ver</label> -->
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="hidden" name="ver" id="ver" value="native"/>
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
		    <!-- <label for="mount_src">mount_src</label> -->
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="hidden" name="mount_src" id="mount_src" value="0"/>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <!-- <label for="mount_obj">mount_obj</label> -->
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="hidden" name="mount_obj" id="mount_obj" value="0"/>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <!-- <label for="mount_kernel">mount_kernel</label> -->
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="hidden" name="mount_kernel" id="mounr_kernel" value="0"/>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <label for="mount_ports">Mount /usr/ports?</label>
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="checkbox" name="mount_ports" id="mount_ports" value="mount_ports"/>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <label for="astart">Autostart:</label>
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="checkbox" name="astart" id="astart" value="astart"/>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <!-- <label for="vnet">vnet</label> -->
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="hidden" name="vnet" id="vnet" value="0"/>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <!-- <label for="applytpl">applytpl</label> -->
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="hidden" name="applytpl" id="applytpl" value="1"/>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <!-- <label for="floatresolv">floatresolv</label> -->
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="hidden" name="floatresolv" id="floatresolv" value="1"/>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <label for="user_pw_root">Root password</label>
		</div>
		<div class="col-md-6">
		    <div class="input-group">
<!--			<input type="password" class="form-control" placeholder="8-20 symbols" name="user_pw_root" id="user_pw_root" maxlength="20" onkeypress="CountPass('user_pw_root', 'root_pass_correct')" onfocus="CountPass('user_pw_root', 'root_pass_correct')" onkeyup="CountPass('user_pw_root', 'root_pass_correct')"/>-->
			<input type="password" class="form-control" placeholder="8-20 symbols" name="user_pw_root" id="user_pw_root" maxlength="20" onkeypress="CountPass('user_pw_root', 'root_pass_correct')" onkeyup="CountPass('user_pw_root', 'root_pass_correct')"/>
			<span class="input-group-addon"><div class="label label-info" id="root_pass_correct"><span class="glyphicon glyphicon-arrow-left"></span></div></span>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <label for="user_pw_root_ag">Root password (Again)</label>
		</div>
		<div class="col-md-6">
		    <div class="input-group">
<!--			<input type="password" class="form-control" placeholder="8-20 symbols" name="user_pw_root_ag" id="user_pw_root_ag" maxlength="20" onkeypress="CorrectPass('user_pw_root_ag', 'user_pw_root', 'root_repass_correct')" onfocus="CorrectPass('user_pw_root_ag', 'user_pw_root', 'root_repass_correct')" onkeyup="CorrectPass('user_pw_root_ag', 'user_pw_root', 'root_repass_correct')"/>-->
			<input type="password" class="form-control" placeholder="8-20 symbols" name="user_pw_root_ag" id="user_pw_root_ag" maxlength="20" onkeypress="CorrectPass('user_pw_root_ag', 'user_pw_root', 'root_repass_correct')" onkeyup="CorrectPass('user_pw_root_ag', 'user_pw_root', 'root_repass_correct')"/>
			<span class="input-group-addon"><div class="label label-info" id="root_repass_correct"><span class="glyphicon glyphicon-arrow-left"></span></div></span>
		    </div>
		</div>
	    </div>
<!--	    <div class="row">
		<div class="col-md-6">
	    	    <label for="user_add">Unprivileged User</label>
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="text" class="form-control" placeholder="Login" maxlength="20" name="user_add" id="user_add"/>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
	    	    <label for="user_add_gecos">GECOS</label>
		</div>
		<div class="col-md-6">
		    <div class="input">
	    		<input type="text" class="form-control" placeholder="Full Name" maxlength="20" name="user_add_gecos" id="user_add_gecos"/>
	    	    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <label for="user_add_password">User Password</label>
		</div>
		<div class="col-md-6">
		    <div class="input-group">-->
<!--			<input type="password" class="form-control" placeholder="8-20 symbols" name="pw_user" id="pw_user" maxlength="20" onkeypress="CountPass('pw_user', 'user_pass_correct')" onfocus="CountPass('pw_user', 'user_pass_correct')" onkeyup="CountPass('pw_user', 'user_pass_correct')"/>-->
<!--			<input type="password" class="form-control" placeholder="8-20 symbols" name="pw_user" id="pw_user" maxlength="20" onkeypress="CountPass('pw_user', 'user_pass_correct')" onkeyup="CountPass('pw_user', 'user_pass_correct')"/>
			<span class="input-group-addon"><div class="label label-info" id="user_pass_correct"><span class="glyphicon glyphicon-arrow-left"></span></div></span>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<div class="col-md-6">
		    <label for="user_add_password_ag">User Password (Again)</label>
		</div>
		<div class="col-md-6">
		    <div class="input-group">-->
<!--			<input type="password" class="form-control" placeholder="8-20 symbols" name="pw_user_ag" id="pw_user_ag" maxlength="20" onkeypress="CorrectPass('pw_user_ag', 'pw_user', 'user_repass_correct')" onfocus="CorrectPass('pw_user_ag', 'pw_user', 'user_repass_correct')" onkeyup="CorrectPass('pw_user_ag', 'pw_user', 'user_repass_correct')"/>-->
<!--			<input type="password" class="form-control" placeholder="8-20 symbols" name="pw_user_ag" id="pw_user_ag" maxlength="20" onkeypress="CorrectPass('pw_user_ag', 'pw_user', 'user_repass_correct')" onkeyup="CorrectPass('pw_user_ag', 'pw_user', 'user_repass_correct')"/>
			<span class="input-group-addon"><div class="label label-info" id="user_repass_correct"><span class="glyphicon glyphicon-arrow-left"></span></div></span>
		    </div>
		</div>
	    </div>-->
		<hr>
	    <div class="row">
		<div class="col-md-6">
		    <label for="sysrc_enable">Enabled services:</label>
		</div>
		<div class="col-md-6">
		    <div class="input">
			<input type="text" readonly="readonly" class="form-control" placeholder="Servises..." name="sysrc_enable" id="sysrc_enable" value=""/>
		    </div>
		</div>
	    </div>
	    <div class="row">
		<?php include 'srvlist.shtml' ?>
	    </div>
	    </form>
	    <div class="modal-footer">
		<input class="inp" type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="xajax_jcreate(xajax.getFormValues('fjcreate')); return false;" value="Create Jail" />
	    </div>
    	</div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
