<?php
require_once('../head.inc.php');
require_once('../cbsd.inc.php');
?>

	<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	<h2 class="sub-header">TaskLog</h2>

<?php
if (!isset($_GET['log'])) {
	echo "Empty log filename";
	exit(0);
} else {
	$logfile=$_GET['log'];
}

$rp=realpath($logfile);


if (!strncmp($rp, '/tmp/', 5)) {
	$fp = fopen($logfile, 'r');
	if ($fp) {
		echo "<pre>";
		while (($buffer = fgets($fp, 4096)) !== false) {
			echo $buffer;
		}
		echo "</pre>";
	} else {
		echo "Cant't open: $logfile";
	}
} else {
	echo "Cant't open: $logfile";
}
?>
