<?php
require_once('../head.inc.php');
require_once('../cbsd.inc.php');

include('../nodes.inc.php');
?>

        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <h1 class="page-header">Bhyve stop</h1>
<?php

if (!isset($_POST['jname'])) {
	echo "Empty jname";
	exit(0);
}

$jname=$_POST['jname'];

$handle=popen("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb mode=new /usr/local/bin/cbsd bstop inter=0 jname=$jname", 'r');
echo "'$handle'; " . gettype($handle) . "\n";
$read = fread($handle, 2096);
echo $read;
pclose($handle);
fflush();
sleep(2);
?>
<script type="text/javascript">
window.location="/bhyvevms/"
</script>
