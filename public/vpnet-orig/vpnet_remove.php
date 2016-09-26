<?php
require_once('../head.inc.php');
require_once('../cbsd.inc.php');

include('../nodes.inc.php');

if (!isset($_GET['idx'])) {
	echo "Empty idx";
	exit(0);
}

if (isset($_GET['sure'])) {
	$sure=1;
} else {
	$sure=0;
}

$idx=$_GET['idx'];

if ($sure==0) {
	$str = <<<EOF
<script type="text/javascript">
<!--

var answer = confirm("Really remove key?")
if (!answer)
window.location="/vpnet/"
else
window.location="vpnet_remove.php?idx=$idx&sure=1"
// -->
</script>
EOF;
	echo $str;
	exit(0);
}

//$handle=popen("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb mode=new /usr/local/bin/cbsd node mode=remove inter=0 node=$idx", 'r');
//$read = fgets($handle, 4096);
//echo "Job Queued: $read";
//pclose($handle);

$dbfilepath="/var/db/webdev/vpnet.sqlite";

$stat = file_exists($dbfilepath);
$str = "";

if (!$stat) {
        echo "$dbfilepath not found";
	sleep(10);
	header( 'Location: authkey.php' ) ;
	die();
}

$db = new SQLite3($dbfilepath); $db->busyTimeout(5000);
$query="DELETE FROM vpnet WHERE idx='{$idx}');";
echo "$query";
$db->exec("DELETE FROM vpnet WHERE idx='{$idx}';");
$db->close();
?>
<script type="text/javascript">
window.location="/vpnet/"
</script>
