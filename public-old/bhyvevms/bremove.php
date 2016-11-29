<?php
require_once('../head.inc.php');
require_once('../cbsd.inc.php');

include('../nodes.inc.php');
?>

        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
        <h1 class="page-header">Bhyve VMs</h1>
<?php
if (!isset($_GET['jname'])) {
    echo "Empty jname";
    exit(0);
}

if (isset($_GET['sure'])) {
    $sure=1;
} else {
    $sure=0;
}

$jname=$_GET['jname'];

if ($sure==0) {
	$str = <<<EOF
<script type="text/javascript">
<!--

var answer = confirm("Really remove $jname VM?")
if (!answer)
window.location="blist.php"
else
window.location="bremove.php?jname=$jname&sure=1"
// -->
</script>
EOF;
	echo $str;
	exit(0);
}

$handle=popen("env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd task owner=cbsdweb mode=new /usr/local/bin/cbsd bremove inter=0 jname=$jname", 'r');
$read = fgets($handle, 4096);
echo "Job Queued: $read";
pclose($handle);
fflush();
sleep(3);
?>
<script type="text/javascript">
window.location="/bhyvevms/"
</script>
