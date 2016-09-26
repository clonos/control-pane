<?php
include_once('../head.inc.php');
require_once('../cbsd.inc.php');

//include('../nodes.inc.php');

?>
	<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
<?php

if (!isset($_GET['jname'])) {
        echo "Empty jname";
        exit(0);
}

if (!isset($_GET['helper'])) {
	echo "Empty helper";
	exit(0);
}

$jname=$_GET['jname'];
$helper=$_GET['helper'];
?>
	<h1 class="page-header">Helper <?php echo $helper; ?></h1>

<?php
if (isset($_GET['mode'])) {
	$mode=$_GET['mode'];
} else {
	$mode="";
}

require_once('imghelper_menu.php');
jail_menu();

include_once('../db.inc.php');

function forms( $dbfilepath ) 
{
	$db = new SQLite3($dbfilepath); $db->busyTimeout(5000);
	
	$query="SELECT idx,group_id,order_id,param,desc,def,cur,new,mandatory,attr,xattr,type FROM forms ORDER BY group_id ASC, order_id ASC";

	$fields = $db->query($query);

	echo '<form name="">';

	while ($row = $fields->fetchArray()) {

		list( $idx , $group_id, $order_id , $param , $desc , $def , $cur , $new , $mandatory , $attr , $xattr , $type ) = $row;

		$tpl=getElement($type, $desc);

		$params=array('param','desc','attr','cur');

		if(isset($cur) && !empty($cur)) $def=$cur;
			$tpl=str_replace('${def}',$def,$tpl);
			
		$required=($mandatory==1)?' required':'';
		$tpl=str_replace('${required}',$required,$tpl);
		echo $tpl;
	}

	echo '</form>';
}
	
function getElement($el, $desc)
{
	$tpl='';

	switch($el)
	{
		case 'inputbox':
			$tpl .= $desc . ":" . '<input type="text" name="${param}" value="${def}" ${attr}${required} /><br>';
			break;
		case 'delimer':
			$tpl .= "<h1>${desc}</h1>";
			break;
	}
	return $tpl;
}


function setButtons($arr=array())
{
	echo '<div class="buttons"><input type="button" value="Apply" /> <input type="button" value="Cancel" /></div>';
}


if ($mode=="install") {
	echo "INSTALL";
	$res=cmd(CBSD_CMD."task owner=cbsdweb mode=new env NOCOLOR=1 /usr/local/bin/cbsd imghelper module=$helper jname=$jname inter=0");

	exit(0);
}


$jail_form=$workdir."/jails-system/".$jname."/helpers/".$helper.".sqlite";

if (file_exists($jail_form)) {
//	$form=new Forms($helper);
//	$form->generate();
	//$form->setButtons(array('apply','cancel'));
	forms( $jail_form );
} else {
	echo "Module not installed for $jname. Please <a href='/img_helper_cfg.php?jname=$jname&mode=install&helper=$helper'>install module</a>";
}
