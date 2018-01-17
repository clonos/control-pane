<?php

$html='';
$db=new Db('base','local');
if($db!==false)
{
	$res=$db->select("select id,name,description,pkg_vm_ram,pkg_vm_disk,pkg_vm_cpus,owner from vmpackages order by name asc");
}

$nth=0;
$hres=$this->getTableChunk('packages','tbody');

$html_tpl=$hres[1];
if(!empty($res))foreach($res as $r)
{
	$html_tpl1=$html_tpl;
	$vars=array(
		'id'=>$r['id'],
		'name'=>$r['name'],
		'description'=>$r['description'],
		'pkg_vm_ram'=>$r['pkg_vm_ram'],
		'pkg_vm_disk'=>$r['pkg_vm_disk'],
		'pkg_vm_cpus'=>$r['pkg_vm_cpus'],
		'owner'=>$r['owner'],
		'edit_title'=>$this->translate('edit_title'),
		'delete_title'=>$this->translate('delete_title'),
	);
	foreach($vars as $var=>$val)
		$html_tpl1=str_replace('#'.$var.'#',$val,$html_tpl1);
	$html.=$html_tpl1;
}


$html=str_replace(array("\n","\r","\t"),'',$html);

echo json_encode(array(
	'tbody'=>$html,
	'error'=>false,
	'func'=>'fillTable',
	'id'=>'packageslist',
));
