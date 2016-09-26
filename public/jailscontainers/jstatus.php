<?php
include_once('../head.inc.php');
require_once('../cbsd.inc.php');

include_once('../nodes.inc.php');

if (!isset($_GET['jname'])) {
	echo "Empty jname";
	exit(0);
} else {
	$jname=$_GET['jname'];
}

include('jail_menu.php');
