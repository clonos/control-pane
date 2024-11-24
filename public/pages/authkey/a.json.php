<?php

$db = new Db('base','authkey');
$res = $db->select("SELECT idx,name,authkey FROM authkey;", []);
$html = '';
$html_tpl = '';


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

			$html_tpl=$hres[1];

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
		'id' => 'authkeyslist'
	];
}