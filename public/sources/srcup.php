<?php
require_once('../head.inc.php');
require_once('../cbsd.inc.php');

include('../nodes.inc.php');
?>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<h2 class="sub-header">FreeBSD source checkout</h2>

<?php
if (!isset($_GET['mode'])) {
	echo "Empty mode";
	exit(0);
}

if (!isset($_GET['ver'])) {
	echo "Empty ver";
	exit(0);
}

$ver=$_GET['ver'];
$mode=$_GET['mode'];

if ($ver=="default") {
	$myver="";
} else {
	$myver="ver=$ver";
}

if ($mode=="remove") {
	echo "Remove..";
	$res=cmd(CBSD_CMD."task owner=cbsdweb mode=new env NOCOLOR=1 /usr/local/bin/cbsd removesrc $myver");
}

if ($mode=="update") {
	echo "Update...";
	$res=cmd(CBSD_CMD."task owner=cbsdweb mode=new env NOCOLOR=1 /usr/local/bin/cbsd srcup $myver");
}
?>

<script type="text/javascript">
window.location="/sources/"
</script>