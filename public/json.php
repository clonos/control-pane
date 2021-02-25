<?php

if(
	!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
	strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest' ||
	!isset($_POST['path'])
){
	echo '{}';
	exit;
}

$path = trim($_POST['path'], DIRECTORY_SEPARATOR);

include('../php/clonos.php');
$clonos = new ClonOS();
$clonos->json_req = true;

exit;