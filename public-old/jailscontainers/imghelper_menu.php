<?php
include_once('../head.inc.php');
require_once('../cbsd.inc.php');

include_once('../nodes.inc.php');


if (!isset($_GET['jname'])) {
        echo "Empty jname";
        exit(0);
}

function jail_menu()
{
	global $jname;

$str = <<<EOF
        <a href="javascript:location.reload(true)">[ Refresh Page ]</a> |
EOF;

$res=cmd(CBSD_CMD."imghelper header=0");

if ($res['retval'] != 0 ) {
	if (!empty($res['error_message']))
		echo $res['error_message'];
	exit(1);
}


$lst=explode("\n",$res['message']);
$n=0;
if(!empty($lst)) foreach($lst as $item)
{
	$str .= <<<EOF
        <a href="img_helper_cfg.php?jname=$jname&helper=$item">[ $item ]</a> |
EOF;
}

echo $str;
echo "<hr>";

}
?>
