<?php
require_once($rp.'/cbsd.inc.php');
require_once($rp.'/left_menu.inc.php');
include_once($rp.'/nodes.inc.php');
?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	<h1 class="sub-header">FreeBSD Bases</h1>
	<div class="table-responsive">
	<table class="table table-striped">
	<thead>
	<tr>
	<!--idx platform name arch targetarch ver stable elf date-->
		<th>Node</th>
		<th>Name</th>
		<th>Platform</th>
		<th>Arch</th>
		<th>TargetArch</th>
		<th>Ver</th>
		<th>Stable</th>
		<th>Elf</th>
		<th>Action</th>
		<th>Remove</th>
	</tr>
	</thead>
	<tbody>

	<?php
	require_once('show_bases.php');
	
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

	show_bases($nodelist);
	?>
	</tbody>
	</table>
	</div>
	</div>
</div>
</div>
