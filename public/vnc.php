<?php
if(!isset($_GET['jname']))
{
	echo 'You forgot to specify a name of jail!';
	exit;
}

$rp=realpath('../');
include($rp.'/php/clonos.php');
$clonos=new ClonOS($rp);
$clonos->runVNC($_GET['jname']);
