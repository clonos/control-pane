<?php

/*
id INTEGER PRIMARY KEY AUTOINCREMENT,
username VARCHAR(150) UNIQUE NOT NULL, 
password VARCHAR(128) UNIQUE NOT NULL, 
first_name VARCHAR(32), 
last_name VARCHAR(32), 
last_login TIMESTAMP DATE, 
is_active BOOLEAN DEFAULT 'true' NULL, 
date_joined TIMESTAMP DATE DEFAULT (datetime('now','localtime'))  
);
*/

$html='';
$db=new Db('clonos');
if($db!==false)
{
	$res=$db->select("select id,username,first_name,last_name,date_joined,last_login,is_active from auth_user order by date_joined desc", []);
}

$nth=0;
$hres=$this->getTableChunk('users','tbody');

$html_tpl=$hres[1];
if(!empty($res))foreach($res as $r)
{
	$html_tpl1=$html_tpl;
	$vars=array(
		'id'=>$r['id'],
		'login'=>$r['username'],
		'first_name'=>$r['first_name'],
		'last_name'=>$r['last_name'],
		'date_joined'=>$r['date_joined'],
		'last_login'=>$r['last_login'],
		'is_active'=>($r['is_active']==1)?'icon-ok':'',
		'edit_title'=>$this->translate('edit_title'),
		'delete_title'=>$this->translate('delete_title'),
	);
	foreach($vars as $var=>$val)
		$html_tpl1=str_replace('#'.$var.'#',$val,$html_tpl1);
	$html.=$html_tpl1;
}


$html=str_replace(array("\n","\r","\t"),'',$html);

/*
echo json_encode(array(
	'tbody'=>$html,
	'error'=>false,
	'func'=>'fillTable',
	'id'=>'userslist',
	//'tasks'=>$tasks,
	//'template'=>$html_tpl_1,
	//'protected'=>$protected,
));
*/
$included_result_array=array(
	'tbody'=>$html,
	'error'=>false,
	'func'=>'fillTable',
	'id'=>'userslist',
);