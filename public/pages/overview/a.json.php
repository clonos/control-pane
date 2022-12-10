<?php
$res_array = [
	'num-nodes' => 1,
	'online-nodes' => 1,
	'offline-nodes' => 0,
	'num-jails' => 0,
	'num-cores' => 0,
	'average' => 0,
	'sum-ram' => 0,
	'sum-storage' => 'Unknown',
	'error' => false,
	'error_message' => ''
];

$nodenames = ['local'];
$db = new Db('base','nodes');
$nodes = $db->select('select nodename,ip from nodelist', []);
foreach($nodes as $node){
	$idle = $this->check_locktime($node['ip']);
	if($idle == 0){
		$res_array['offline-nodes']++;
	} else {
		$res_array['online-nodes']++;
	}
	$nodenames[] = $node['nodename'];
}

// extra+1: мы предполагаем, что сервер с WEB интерфейсом
// также играет роль ноды - ее можно использовать полноценно со
// всеми ресурсами
$res_array['num-nodes'] = count($nodes) + 1;

foreach($nodenames as $name){
	$ndb = new Db('base', trim($name));
	if($ndb->error){
		$included_result_array = ['error' => true, 'error_message' => $ndb->error_message];
		exit;
	}

	$jcounts = $ndb->selectOne('SELECT COUNT(*) as count FROM jails;', []);
	$res_array['num-jails'] += $jcounts['count'];

	$counts = $ndb->select('SELECT ncpu,physmem,cpufreq FROM local;', []);
	foreach($counts as $cel){
		$res_array['num-cores'] += $cel['ncpu'];
		$res_array['sum-ram'] += $cel['physmem'];
		$res_array['average'] += $cel['cpufreq'];
	}
}

if($res_array['average'] > 0){
	$res_array['average'] = $this->GhzConvert($res_array['average']/($res_array['num-nodes']?:1));
}

$res_array['sum-ram'] = $this->fileSizeConvert((int) $res_array['sum-ram'], 1024, true);

$included_result_array = $res_array;