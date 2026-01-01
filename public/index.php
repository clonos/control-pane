<?php
if(preg_match('/(?i)msie [5-9]/',$_SERVER['HTTP_USER_AGENT']))
{
	echo '<!DOCTYPE html><div style="margin-top:10%;text-align:center;font-size:large;color:darkred;"><p>Sorry, your browser is not supported!</p><p>Please, use last version of any browser.</p></html>';
	exit;
}

//$_real_path=realpath('../');
$_real_path=getenv('HOME').'/clonos';

require_once($_real_path.'/php/new/clonos.php');
$clonos=new ClonOS($_real_path);
$clonos->start();
