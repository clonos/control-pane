<?php

$db = new Db('base','nodes');
$nodes = $db->select("select nodename,ip from nodelist order by nodename desc", []);
$nodes[] = ['nodename' => 'local'];
$nodes = array_reverse($nodes);

$ids = [];
$nth = 0;
$html = '';
$html_tpl = '';
$hres = $this->getTableChunk('baseslist','tbody');


foreach($nodes as $node){

	$db1 = new Db('base', $node['nodename']);
	if(!$db1->error){

		$bases = $db1->select("SELECT idx,platform,name,arch,targetarch,ver,stable,elf,date FROM bsdbase order by cast(ver AS int)", []);
		$num = $nth & 1;

		foreach($bases as $base){

			Utils::clonos_syslog("bases a.json.php base loop: base id=". $base['idx']);
			$idle = 1;
			if($node['nodename'] != 'local'){
				$idle = $this->check_locktime($node['ip']);
			}

			$ids[] = $base['idx'];
			$id = 'base'.$base['ver'].'-'.$base['arch'].'-'.$base['stable'];
			
			if($hres !== false){
				$vars = [
					'id' => $id,
					'nth-num' => 'nth'.$num,
					'node' => $node['nodename'],
					'name' => $base['name'],
					'platform' => $base['platform'],
					'arch' => $base['arch'],
					'targetarch' => $base['targetarch'],
					'version' => $base['ver'],
					'version1' => ($base['stable']==1) ? 'stable' : 'release',
					'elf' => $base['elf'],
					'date' => $base['date'],
					'jstatus' => '',
					'maintenance' => ($idle == 0) ? ' maintenance' : '',
					'deltitle' => $this->translate('Delete')
				];

				Utils::clonos_syslog("bases a.json.php base loop: hres != false: ". implode(",", $vars));
				$html_tpl=$hres[1];

				foreach($vars as $var => $val){
					Utils::clonos_syslog("bases a.json.php replace [#".$var."#] by val: ".$val." hres = ". $hres[1]);
					$html_tpl = str_replace('#'.$var.'#', $val, $html_tpl);
//					$html .= $html_tpl;
					Utils::clonos_syslog("bases a.json.php replace result: ".$html_tpl);
				}
				$html .= $html_tpl;
			}

			$ids[]='#'.$id;
		}
		$nth++;
	}
}

$tasks = (empty($ids)) ? '' : $tasks = $this->getRunningTasks($ids);

if($hres !== false){
	$html_tpl = str_replace(["\n","\r","\t"], '', $hres[1]);
	$vars = [
		'nth-num' => 'nth0',
		'status' => '',
		//'jstatus' => $this->translate('Updating'),
		//'icon' => 'spin6 animate-spin',
		'desktop' => ' s-off',
		'maintenance' => ' maintenance busy',
		'updtitle' => $this->translate('Update'),
		'deltitle' => $this->translate('Delete')
	];

	foreach($vars as $var => $val){
		$html_tpl = str_replace('#'.$var.'#', $val, $html_tpl);
	}
}

$included_result_array = [
	'tbody' => str_replace(["\n","\r","\t"], '', $html),
	'error' => false,
	'func' => 'fillTable',
	'id' => 'baseslist',
	'tasks' => $tasks,
	'template' => $html_tpl
];