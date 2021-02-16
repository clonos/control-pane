<?php

$db=new Db('base','authkey');
$res=$db->select('SELECT idx,name,authkey FROM authkey;', []);

$html='';
if($res!==false)
{
	$nth=0;
	$num=$nth & 1;

	if(!empty($res)) foreach($res as $item)
	{
		$hres=$this->getTableChunk('authkeyslist','tbody');
		if($hres!==false)
		{
			$html_tmp=$hres[1];
			$vars=array(
				'nth-num'=>'nth'.$num,
				'keyid'=>$item['idx'],
				'keyname'=>$item['name'],
				'keysrc'=>$item['authkey'],
				'deltitle'=>' title="'.$this->translate('Delete').'"',
			);
			
			foreach($vars as $var=>$val)
				$html_tmp=str_replace('#'.$var.'#',$val,$html_tmp);
			
			$html.=$html_tmp;
		}
	}
	/*
	echo json_encode(array(
		'tbody'=>$html,
		'error'=>false,
		'func'=>'fillTable',
		'id'=>'authkeyslist',
	));
	*/
	$included_result_array=array(
		'tbody'=>$html,
		'error'=>false,
		'func'=>'fillTable',
		'id'=>'authkeyslist',
	);
}