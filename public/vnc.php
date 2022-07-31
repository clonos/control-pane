<?php

if(!isset($_GET['jname'])){
	echo 'You forgot to specify a name of jail!';
	exit;
}

function runVNC($jname)
{
	$res=(new Db('base','local'))->selectOne("SELECT vnc_password FROM bhyve WHERE jname=?", array([$jname]));

	$pass='cbsd';
	if($res!==false) $pass=$res['vnc_password'];

	$remote_ip=$_SERVER['REMOTE_ADDR'];

	CBSD::run("vm_vncwss jname=%s permit=%s", array($jname,$remote_ip));

	// HTTP_HOST is preferred for href
	if (isset($_SERVER['HTTP_HOST']) && !empty(trim($_SERVER['HTTP_HOST']))){
		$nodeip=$_SERVER['HTTP_HOST'];
	}

	if (filter_var($nodeip, FILTER_VALIDATE_IP)) {
		$is_ip4=true;
	} else {
		$is_ip4=false;
	}

	if ($is_ip4 == false) {
		if (filter_var($nodeip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			$is_ip6=true;
		} else {
			$is_ip6=false;
		}
	}

	// HTTP_HOST is IP, try to check SERVER_NAME
	if (($is_ip4==true)||($is_ip6==true)) {
		if(isset($_SERVER['SERVER_NAME']) && !empty(trim($_SERVER['SERVER_NAME']))){
			$nodeip=$_SERVER['SERVER_NAME'];
		} else {
			$nodeip=$_SERVER['SERVER_ADDR'];
		}
	}

	// handle when 'server_name _;' - use IP instead
	if (strcmp($nodeip, "_") == 0) {
		$nodeip=$_SERVER['SERVER_ADDR'];
	}

	# TODO: This will send the pass in clear text
	header('Location: http://'.$nodeip.':6081/vnc_lite.html?scale=true&host='.$nodeip.'&port=6081?password='.$pass);
	exit;
}

$rp=realpath('../');
require_once($rp.'/php/db.php');
require_once($rp.'/php/cbsd.php');

runVNC($_GET['jname']);
