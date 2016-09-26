<?php
require_once($rp.'/cbsd.inc.php');
require_once($rp.'/left_menu.inc.php');
include_once($rp.'/nodes.inc.php');
?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	<h1 class="page-header">Bhyve VMs</h1>
	<div class="row placeholders">
		<div class="col-xs-6 col-sm-2 placeholder">
			<?php
			$dbfilepath=$workdir."/var/db/authkey.sqlite";

			$db = new SQLite3($dbfilepath); $db->busyTimeout(5000);
			$authkeyres = $db->query('SELECT idx,name,authkey FROM authkey;');

			$authkey_list="";

			if (!($authkeyres instanceof Sqlite3Result)) {
				echo "Error: $dbfilepath";
				$gwinfo="unable to fetch authkey";
			} else {
				while ($row = $authkeyres->fetchArray()) {
					list( $idx , $name, $authkey ) = $row;
					$authkey_list .= <<<EOF
<option value="$name">$name</option>
EOF;
				}
			}

			$db->close();

			# Select for OS iso
			$dbfilepath=$workdir."/var/db/authkey.sqlite";

			$db = new SQLite3($dbfilepath); $db->busyTimeout(5000);
			$authkeyres = $db->query('SELECT idx,name,authkey FROM authkey;');

			$os_iso_list="<option value=\"profile\">profile</option>";

			$db->close();

			?>
			<a href="#bcreate" data-toggle="modal" data-target="#bcreate">
			<div class="btn btn-success">
				<span style="font-size: 30px" class="glyphicon glyphicon-plus gi-5x"></span>
			</div>
			</a>
			<a href="#bcreate" data-toggle="modal" data-target="#bcreate"><h4>New VMs from ISO</h4></a>
			<?php
				require_once("bcreate.inc");
			?>
			</div>
		<div class="col-xs-6 col-sm-2 placeholder">
			<a href="#bcreate" data-toggle="modal" data-target="#bcreate">
			<div class="btn btn-success">
				<span style="font-size: 30px" class="glyphicon glyphicon-plus gi-5x"></span>
			</div>
			</a>
			<a href="#bobtain" data-toggle="modal" data-target="#bobtain"><h4>Obtain VMs</h4></a>

			<?php
			
			require_once("bobtain.inc");
			?>

		</div>
	</div>

	<h2 class="sub-header">Bhyve</h2>
	<div class="table-responsive">
		<table class="table table-striped">
		<thead>
		<tr>
			<th>node</th>
			<th>vm</th>
			<th>vm_ram</th>
			<th>vm_cpus</th>
			<th>vm_os_type</th>
			<th>status</th>
			<th>action</th>
			<th>remove</th>
			<th>VNC</th>
		</tr>
		</thead>
		<tbody>
		<?php
		require_once('show_bhyvevm.php');

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

		show_bhyvevm($nodelist);
		?>

		</tbody>
		</table>
	</div>
	</div>
	</div>
</div>
