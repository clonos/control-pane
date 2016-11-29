<?php
require_once($rp.'/cbsd.inc.php');
require_once($rp.'/left_menu.inc.php');
include_once($rp.'/nodes.inc.php');
?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	<h1 class="page-header">CBSD Nodes</h1>
	<div class="row placeholders">
		<div class="col-xs-6 col-sm-2 placeholder">
			<a href="#nodeadd" data-toggle="modal" data-target="#nodeadd">
			<div class="btn btn-success">
				<span style="font-size: 30px" class="glyphicon glyphicon-plus gi-5x"></span>
			</div>
			</a>
			<a href="#nodeadd" data-toggle="modal" data-target="#nodeadd"><h4>Add Nodes</h4></a>
			<?php
				require_once("node_add.inc");
			?>
		</div>
	</div>

	<h2 class="sub-header">Nodes</h2>
	<div class="table-responsive">
		<table class="table table-striped">
		<thead>
		<tr>
		<th>Nodename</th>
		<th>Main IP</th>
		<th>Descr</th>
		<th>Location</th>
		<th>Sys</th>
		<th>FS</th>
		<th>Memory</th>
		<th>CPU</th>
		<th>CPU Freq.</th>
		<th>Notes</th>
		<th>Action</th>
		</tr>
		</thead>
		<tbody>
		<?php
		require_once('show_nodes.php');

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

		show_nodes();
		?>

		</tbody>
		</table>
	</div>
	</div>
	</div>
</div>