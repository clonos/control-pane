<?php
if(!isset($_GET['jname'])){
	echo 'You forgot to specify a name of jail!';
	exit;
}

function runVNC($jname)
{
	$db=new Db('base','local');
	$res=db->selectOne("SELECT vnc_password FROM bhyve WHERE jname=?", array([$jname]));

	$pass='cbsd';
	if($res!==false) $pass=$res['vnc_password'];

	CBSD::run("vm_vncwss jname=%s permit=%s", $_SERVER['REMOTE_ADDR']);

	if(isset($_SERVER['SERVER_NAME']) && !empty(trim($_SERVER['SERVER_NAME']))){
		$nodeip=$_SERVER['SERVER_NAME'];
	} else {
		$nodeip=$_SERVER['SERVER_ADDR'];
	}

	# TODO: This will send the pass in clear text
	header('Location: http://'.$nodeip.':6081/vnc_auto.html?host='.$nodeip.'&port=6081?password='.$pass);
	exit;
}

$rp=realpath('../');
require_once($rp.'/php/db.php');
require_once($rp.'/php/cbsd.php');

runVNC($_GET['jname']);
