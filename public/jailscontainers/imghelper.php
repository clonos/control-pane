<?php
include_once('../head.inc.php');
require_once('../cbsd.inc.php');

include_once('../nodes.inc.php');

if (!isset($_GET['jname'])) {
        echo "Empty jname";
        exit(0);
}

$jname=$_GET['jname'];
?>

	<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
	<h1 class="page-header">Helper</h1>
	<?php
	include('imghelper_menu.php');
	jail_menu();
	?>
