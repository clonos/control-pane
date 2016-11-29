<?php
//require_once($_SERVER['DOCUMENT_ROOT'].'/xajax_core/xajax.inc.php');
require_once('xajax_core/xajax.inc.php');
$xajax=new xajax('https://'.$_SERVER['HTTP_HOST'].'/fun.server.php');
$xajax->configure('javascript URI','/');
//$xajax->configure('debug', true);

//$xajax->register(XAJAX_FUNCTION, 'jstop', array(
//                'mode' => '"synchronous"'
//                ));
//$xajax->register(XAJAX_FUNCTION, 'jstart', array(
//                'mode' => '"synchronous"'
//                ));
$xajax->register(XAJAX_FUNCTION, 'bstop');
$xajax->register(XAJAX_FUNCTION, 'bstart');
$xajax->register(XAJAX_FUNCTION, 'bremove');
$xajax->register(XAJAX_FUNCTION, 'bcreate');
$xajax->register(XAJAX_FUNCTION, 'bobtain');
$xajax->register(XAJAX_FUNCTION, 'bclone');
$xajax->register(XAJAX_FUNCTION, 'brename');
$xajax->register(XAJAX_FUNCTION, 'jstop');
$xajax->register(XAJAX_FUNCTION, 'jstart');
$xajax->register(XAJAX_FUNCTION, 'jremove');
$xajax->register(XAJAX_FUNCTION, 'jcreate');
$xajax->register(XAJAX_FUNCTION, 'jclone');
$xajax->register(XAJAX_FUNCTION, 'jrename');
$xajax->register(XAJAX_FUNCTION, 'nodeadd');
$xajax->register(XAJAX_FUNCTION, 'noderemove');
$xajax->register(XAJAX_FUNCTION, 'authkeyadd');
$xajax->register(XAJAX_FUNCTION, 'authkeyremove');
$xajax->register(XAJAX_FUNCTION, 'vpnetadd');
$xajax->register(XAJAX_FUNCTION, 'vpnetremove');
$xajax->register(XAJAX_FUNCTION, 'srcupdate');
$xajax->register(XAJAX_FUNCTION, 'srcremove');
$xajax->register(XAJAX_FUNCTION, 'lang_select');
$xajax->register(XAJAX_FUNCTION, 'flushtasklog');
$xajax->register(XAJAX_FUNCTION, 'launchvnc');

$xajax->register(XAJAX_FUNCTION, 'check_ip', array(
                'mode' => '"synchronous"'
                ));
?>
