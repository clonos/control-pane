<?php
$html='';

$db=new Db('base','nodes');
$res=$db->select('select nodename from nodelist');
$nodes=array('local');
if(!empty($res))foreach($res as $val) $nodes[]=$val['nodename'];

$allnodes=array();

$nth=0;
if(!empty($nodes))foreach($nodes as $node)
{
	$db1=new Db('base',$node);
	if($db1!==false)
	{
		$jails=$db1->select("SELECT jname,ip4_addr,status,hidden FROM jails WHERE emulator != 'bhyve';");
		$allnodes[$node]=$jails;
		
		$num=$nth & 1;
		if(!empty($jails)) foreach($jails as $jail)
		{
			$html.="<tr class=\"nth{$num}\"> <td>{$node}</td> <td class=\"txtleft\">{$jail['jname']}</td> <td class=\"txtleft\">{$jail['ip4_addr']}</td> <td>{$jail['status']}</td> <td class=\"ops\"><span class=\"icon-cnt\"><span class=\"icon-play\"></span></span></td></tr>";
		}
		
		$nth++;
	}
}

echo json_encode(array(
	'html'=>$html,
	'error'=>false,
	'func'=>'fillTable',
	'id'=>'jailslist'
));
