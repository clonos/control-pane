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
$html_tpl='';

$hres=$this->getTableChunk('impslist','tbody');
if($hres!==false)
{
	$html_tmp=$hres[1];
	$html_tmp=replaceVars($html_tmp,array(
		'deltitle'=>' title="'.$this->translate('Delete').'"',
		'dnldtitle'=>' title="'.$this->translate('Download').'"',
		'imptitle'=>' title="'.$this->translate('Create').'"')
	);
	$html_tpl_1=$html_tmp;
}

if(!empty($images)) foreach($images as $item)
{
	if(!isset($item['type'])) $item['type']='unknown';
	//$hres=$this->getTableChunk('impslist','tbody');
	if($hres!==false)
	{
		/*
		$html_tmp=$hres[1];
		$html_tmp=replaceVars($html_tmp,array(
			'deltitle'=>' title="'.$this->translate('Delete').'"',
			'dnldtitle'=>' title="'.$this->translate('Download').'"',
			'imptitle'=>' title="'.$this->translate('Create').'"')
		);
		$html_tpl=$html_tmp;
		*/
		$html_tpl=$html_tmp;
		$filename=$this->media_import.$item['name'];
		$sizefilename=$filename.'.size';
		if(file_exists($sizefilename))
		{
			$size=file_get_contents($sizefilename);
		}else{
			$size=filesize($filename);
		}
		$filesize=$this->fileSizeConvert($size,1024,true);
		
		$vars=array(
			'nth-num'=>'nth'.$num,
			'id'=>$item['name'],
			'jname'=>$item['name'],
			'impsize'=>$filesize,
			'jstatus'=>'',
			'imptype'=>$this->translate($item['type']),
		);
		
		foreach($vars as $var=>$val)
			$html_tpl=str_replace('#'.$var.'#',$val,$html_tpl);
		//	$html_tmp=str_replace('#'.$var.'#',$val,$html_tmp);
		
		$html.=$html_tpl;
	}
}

function replaceVars($tpl,$vars)
{
	foreach($vars as $var=>$val)
		$tpl=str_replace('#'.$var.'#',$val,$tpl);
	return $tpl;
}

echo json_encode(array(
	'tbody'=>$html,
	'error'=>false,
	'func'=>'fillTable',
	'id'=>'impslist',
	'template'=>$html_tpl_1,
));
