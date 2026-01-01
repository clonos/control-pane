<?php

$_real_path=realpath('../');
$_real_path=GETENV('HOME').'/clonos';
$run_mode=php_sapi_name();

require_once($_real_path.'/php/new/clonos.php');

$clonos=new ClonOS($_real_path,true);
if($run_mode=='cli')
{
	$clonos->setup();
}else{
	$clonos->start();
}
