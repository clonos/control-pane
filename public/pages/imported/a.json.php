<?php

function replaceVars($tpl,$vars){
	foreach($vars as $var => $val){
		$tpl = str_replace('#'.$var.'#', $val, $tpl);
	}
	return $tpl;
}

$res = [
//	'item' => [
		0 => [
			'id' => 1,
			'name' => 'test',
			'path' => 'test/test/',
			'type' => 'клетка'
		]
//	]
];

$images = $this->getImportedImages();

$html = '';
$html_tpl_1 = '';
$nth = 0;
$num = $nth & 1;
$html_tpl = '';

$hres = $this->getTableChunk('impslist','tbody');
if($hres !== false){
	$html_tpl_1 = replaceVars($hres[1], [
		'deltitle' => ' title="'.$this->translate('Delete').'"',
		'dnldtitle' => ' title="'.$this->translate('Download').'"',
		'imptitle' => ' title="'.$this->translate('Create').'"'
	]);
}

foreach($images as $item){
	if(!isset($item['type'])){
		$item['type'] = 'unknown';
	}
	//$hres=$this->getTableChunk('impslist','tbody');
	if($hres !== false){
		$html_tpl = $html_tpl_1;
		$filename = $this->media_import.$item['name'];
		$sizefilename = $filename.'.size';
		if(file_exists($sizefilename)){
			$size = file_get_contents($sizefilename);
		} else {
			$size = filesize($filename);
		}
		$filesize = $this->fileSizeConvert($size, 1024, true);
		$query = "select count(*) as busy from taskd where status<2 and jname=?";
		$busy = $this->_db_tasks->selectOne($query, [$item['jname'],PDO::PARAM_STR]);
		$jstatus = '';
		$jbusy = '';
		if($busy['busy'] == 1){
			$jstatus = $this->translate('Exporting');
			$jbusy = 'busy';
		}

		$vars = [
			'nth-num' => 'nth'.$num,
			'id' => $item['name'],
			'jname' => $item['name'],
			'impsize' => $filesize,
			'jstatus' => $jstatus,
			'busy' => $jbusy,
			'imptype' => $this->translate($item['type'])
		];

		foreach($vars as $var => $val){
			$html_tpl = str_replace('#'.$var.'#', $val, $html_tpl);
		}
		$html .= $html_tpl;
	}
}

$included_result_array = [
	'tbody' => $html,
	'error' => false,
	'func' => 'fillTable',
	'id' => 'impslist',
	'template' => $html_tpl_1
];