<?php

$db=new Db('base','storage_media');
$res=$db->select('SELECT idx,name,path,jname FROM media where type="iso"');

$html='';
if($res!==false)
{
	$nth=0;
	$num=$nth & 1;

	if(!empty($res)) foreach($res as $item)
	{
		$hres=$this->getTableChunk('mediaslist','tbody');
		if($hres!==false)
		{
			$html_tmp=$hres[1];
			$vars=array(
				'nth-num'=>'nth'.$num,
				'mediaid'=>$item['idx'],
				'medianame'=>$item['name'],
				'mediapath'=>$item['path'],
				'jname'=>$item['jname'],
				'deltitle'=>' title="'.$this->translate('Delete').'"',
			);
			
			foreach($vars as $var=>$val)
				$html_tmp=str_replace('#'.$var.'#',$val,$html_tmp);
			
			$html.=$html_tmp;
		}
	}

	echo json_encode(array(
		'tbody'=>$html,
		'error'=>false,
		'func'=>'fillTable',
		'id'=>'mediaslist',
	));
}