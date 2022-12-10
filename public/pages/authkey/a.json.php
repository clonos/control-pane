<?php

$db = new Db('base','authkey');
$res = $db->select('SELECT idx,name,authkey FROM authkey;', []);
$html = '';

if($res !== false){
	$nth = 0;
	$num = $nth & 1;

	foreach($res as $item){
		$hres = $this->getTableChunk('authkeyslist', 'tbody');
		if($hres !== false){
			$vars = [
				'nth-num' => 'nth'.$num,
				'keyid' => $item['idx'],
				'keyname' => $item['name'],
				'keysrc' => $item['authkey'],
				'deltitle' => ' title="'.$this->translate('Delete').'"'
			];

			foreach($vars as $var => $val){
				$html_tmp = str_replace('#'.$var.'#', $val, $hres[1]);
			}
			$html .= $html_tmp;
		}
	}

	$included_result_array = [
		'tbody' => $html,
		'error' => false,
		'func' => 'fillTable',
		'id' => 'authkeyslist'
	];
}