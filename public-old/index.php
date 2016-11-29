<?php
//global $lang;
session_start();
require_once'cbsd.inc.php';
require_once'head.inc.php';
require($rp.'/nodes.inc.php');
require_once'fun.server.common.php';
$xajax->printJavascript();
?>
  <body>
    <noscript>
    <div style="color:white;background-color:red;text-align:center;font-size:18px;padding:35px;margin-top:20px;"
	    <p align="center">У вас в браузере отключена поддержка Javascript, без нее вы не сможете работать с данным ПО.</p>
    </div>
    </noscript>
<?php
if(!isset($_GET['mod'])){
    $mod="homepage";
}else{
    $mod=$_GET['mod'];
}
if(!file_exists($rp."/".$mod."/index.inc")){
    $mod="homepage";
}
include($rp."/".$mod."/index.inc");

require($rp.'/footer.inc.php');
?>
