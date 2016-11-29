<?php
require_once($rp.'/cbsd.inc.php');
require_once($rp.'/left_menu.inc.php');
include_once($rp.'/nodes.inc.php');

if (!isset($_GET['jname'])) {
	$jname="%NOJNAME%";
} else {
	$jname=$_GET['jname'];
	
	if (!isset($_GET['jnode'])) {
		$jnode="local";
	} else {
		$jnode=$_GET['jnode'];
	}

}
?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	<h1 class="page-header">Jails containers</h1>
	<div class="row placeholders">
		<div class="col-xs-6 col-sm-2 placeholder">
			<a href="#jcreate" data-toggle="modal" data-target="#jcreate">
			<div class="btn btn-success">
				<span style="font-size: 30px" class="glyphicon glyphicon-plus gi-5x"></span>
			</div>
			</a>
		<a href="#jcreate" data-toggle="modal" data-target="#jcreate"><h4>New Jail</h4></a>
		
		<?php
		$res=cmd(CBSD_CMD.'freejname');

		if ($res['retval'] != 0 ) {
			$freejname="newjail";
		} else {
			$freejname=$res['message'];
		}

		//load data
		$db = new SQLite3("$workdir/var/db/local.sqlite"); $db->busyTimeout(5000);
		$sql = "SELECT ip4_addr,host_hostname,allow_mount,devfs_ruleset,interface,baserw,mount_ports,astart,vnet,ver,allow_sysvipc FROM jails where jname=\"$jname\"";
		$result = $db->query($sql);//->fetchArray(SQLITE3_ASSOC);
		$row = $result->fetchArray();
		list($ip4_addr,$host_hostname,$allow_mount,$devfs_ruleset,$interface,$baserw,$mount_ports,$astart,$vnet,$ver,$allow_sysvipc)=$row;
		require("jcreate.inc");
		require("jconfig.inc");
		?>
		</div>
		<?php
		if ($jname!="%NOJNAME%") {
			require_once('jail_menu.php');
		}
		?>
	</div>

	<h2 class="sub-header">Jails</h2>
	<div class="table-responsive">
		<table class="table table-striped">
		<thead>
		<tr>
			<th>Node</th>
			<th>Jname</th>
			<th>Ip_Addr</th>
			<th>Status</th>
			<th>Jaction</th>
			<th>Remove</th>
			<th>VNC</th>
		</tr>
		</thead>
		<tbody>

		<?php
		require_once('show_jails.php');

		$db = new SQLite3("$workdir/var/db/nodes.sqlite"); $db->busyTimeout(5000);
		$sql = "SELECT nodename FROM nodelist";
		$result = $db->query($sql);//->fetchArray(SQLITE3_ASSOC);
		$row = array();
		$i = 0;

		$nodelist="local";

		while($res = $result->fetchArray(SQLITE3_ASSOC)){
			if(!isset($res['nodename'])) continue;
				$nodelist=$nodelist." ".$res['nodename'];
		}

		show_jails($nodelist);
		?>

		</tbody>
		</table>
		</div>
	</div>
</div>
</div>
