<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
	strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest' ||
		!isset($_POST['path']))
			{echo '{}';exit;}

$_ds=DIRECTORY_SEPARATOR;
$path=trim($_POST['path'],$_ds);
	
$_REALPATH=realpath('../');
include($_REALPATH.'/php/clonos.php');
$clonos=new ClonOS($_REALPATH);
$clonos->json_req=true;

//$file_path=$_REALPATH.$_ds.'public/pages'.$_ds.$path.$_ds;
//$clonos->json_name=$file_path.'a.json.php';

//if(file_exists($json_name)) include($json_name); else echo '{}';
exit;