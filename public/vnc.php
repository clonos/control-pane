<?php

if(!isset($_GET['jname'])){
	echo 'You forgot to specify a name of jail!';
	exit;
}

function runVNC($jname)
{
	$res = (new Db('base','local'))->selectOne("SELECT vnc_password FROM bhyve WHERE jname=?", array([$jname]));

	$pass = ($res !== false) ? $res['vnc_password'] : 'cbsd';

	CBSD::run("vm_vncwss jname=%s permit=%s", array($jname, $_SERVER['REMOTE_ADDR']));

	// HTTP_HOST is preferred for href
	if (isset($_SERVER['HTTP_HOST']) && !empty(trim($_SERVER['HTTP_HOST']))){
		$nodeip = $_SERVER['HTTP_HOST'];
	} else {
		# use localhost as fallback in case the HTTP_HOST header is not set
		$nodeip = '127.0.0.1';
	}

	// HTTP_HOST is IP, try to check SERVER_NAME
	if (filter_var($nodeip, FILTER_VALIDATE_IP)) {
		$nodeip = $_SERVER['SERVER_ADDR'];
		// https://www.php.net/manual/en/reserved.variables.server.php
		// Note: Under Apache 2, you must set UseCanonicalName = On and ServerName. 
		// handle when 'server_name _;' - use IP instead
		if(isset($_SERVER['SERVER_NAME']) && !empty(trim($_SERVER['SERVER_NAME'])) && (strcmp($_SERVER['SERVER_NAME'], "_") != 0)){
			$nodeip = $_SERVER['SERVER_NAME'];
		}
	}

	# TODO: This will send the pass in clear text
	header('Location: http://'.$nodeip.':6081/vnc_lite.html?scale=true&host='.$nodeip.'&port=6081?password='.$pass);
	exit;
}

$rp = realpath('../');
require_once($rp.'/php/db.php');
require_once($rp.'/php/cbsd.php');
require_once($rp.'/php/validate.php');

runVNC(Validate::short_string($_GET['jname'], 32));