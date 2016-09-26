<?php
require_once('../head.inc.php');
require_once('../cbsd.inc.php');

include('../nodes.inc.php');

$vpnet = $_POST['vpnet'];
$vpnet_name = $_POST['vpnet_name'];

if (empty($vpnet)) {
	echo "No such vpnet";
	exit;
}

if (empty($vpnet_name)) {
	echo "No such vpnet_name";
	exit;
}

//echo "OK: $vpnet and $vpnet_name";

$dbfilepath="/var/db/webdev/vpnet.sqlite";

$db = new SQLite3($dbfilepath); $db->busyTimeout(5000);
//$db->exec('PRAGMA journal_mode = wal;');

//$query="insert into vpnet (name,vpnet) ('{$vpnet_name}','{$vpnet}');";

//echo "$query";

$db->exec("INSERT INTO vpnet (name, vpnet) VALUES ('{$vpnet_name}','{$vpnet}')");

//$db->exec($query);
$db->close();


echo "OK: $vpnet and $vpnet_name added";
?>

<script type="text/javascript">
window.location="/vpnet/"
</script>
