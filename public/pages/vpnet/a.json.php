<?php

$db = new Db('base','vpnet');
$res = $db->select("SELECT idx,name,vpnet FROM vpnet", []);
$html = '';

if(!$db->error){
	$nth = 0;
	$num = $nth & 1;

	foreach($res as $item){
		$hres = $this->getTableChunk('vpnetslist','tbody');
		if($hres !== false){
			$html_tmp = $hres[1];
			$vars = [
				'nth-num' => 'nth'.$num,
				'netid' => $item['idx'],
				'netname' => $item['name'],
				'network'=> $item['vpnet'],
				'deltitle' => ' title="'.$this->translate('Delete').'"'
			];

			foreach($vars as $var => $val){
				$html_tmp = str_replace('#'.$var.'#', $val, $html_tmp);
			}
			$html .= $html_tmp;
		}
	}

	$included_result_array = [
		'tbody' => $html,
		'error' => false,
		'func' => 'fillTable',
		'id' => 'vpnetslist'
	];
}