<?php

$res=array(
//	'item'=>array(
		0=>array(
			'id'=>1,
			'name'=>'test',
			'path'=>'test/test/',
			'type'=>'клетка',
		)
//	)
);

$images=$this->getImportedImages();


$html='';
$nth=0;
$num=$nth & 1;

if(!empty($images)) foreach($images as $item)
{
	if(!isset($item['type'])) $item['type']='unknown';
	$hres=$this->getTableChunk('impslist','tbody');
	if($hres!==false)
	{
		$html_tmp=$hres[1];
		$vars=array(
			'nth-num'=>'nth'.$num,
			'impid'=>$item['name'],
			'impname'=>$item['name'],
			//'imppath'=>$item['path'],
			'imptype'=>$this->translate($item['type']),
			'deltitle'=>' title="'.$this->translate('Delete').'"',
			'dnldtitle'=>' title="'.$this->translate('Download').'"',
			'imptitle'=>' title="'.$this->translate('Create').'"',
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
	'id'=>'impslist',
));
