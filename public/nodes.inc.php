<?php
require_once('cbsd.inc.php');

// return 0 when no lock file_exists
// $idle=check_locktime("199.48.133.74");
// if ( $idle > 0 ) echo "ONLINE";
function check_locktime($nodeip)
{
	global $workdir;

	$lockfile="${workdir}/ftmp/shmux_${nodeip}.lock";
	if (!file_exists($lockfile)) {
		return 0;
	}

	$cur_time = time();
	$st_time=filemtime($lockfile);
	
	$difftime=(( $cur_time - $st_time ) / 60 );
	if ( $difftime > 1 ) {
		return round($difftime);;
	} else {
		return 0; //lock exist but too fresh
	}
}

// $ip=get_node_info("n0.olevole.ru","ip");
// echo $ip;
function get_node_info($nodename,$value)
{
	global $workdir;

	$db = new SQLite3("$workdir/var/db/nodes.sqlite"); $db->busyTimeout(5000);
	if (!$db) return;
	$sql = "SELECT $value FROM nodelist WHERE nodename=\"$nodename\"";

	$result = $db->query($sql);//->fetchArray(SQLITE3_ASSOC);
	$row = array();

	while($res = $result->fetchArray(SQLITE3_ASSOC)){
		if(!isset($res["$value"])) return;
		return $res["$value"];
	}
}
?>
