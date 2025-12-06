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
$nodes = $db->select("select nodename,ip from nodelist", []);

// Проверяем, есть ли ошибка в результате запроса
if (isset($nodes['error']) && $nodes['error']) {
	$res_array['error'] = true;
	$res_array['error_message'] = $nodes['info'];
	$included_result_array = $res_array;
	exit;
}

// Проверяем, что $nodes является массивом и не пустой
if (!is_array($nodes)) {
	$res_array['error'] = true;
	$res_array['error_message'] = 'Invalid database response';
	$included_result_array = $res_array;
	exit;
}

foreach($nodes as $node){
	// Проверяем, что $node является массивом с нужными ключами
	if (!is_array($node) || !isset($node['ip']) || !isset($node['nodename'])) {
		continue; // Пропускаем некорректные записи
	}
	
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

	$jcounts = $ndb->selectOne("SELECT COUNT(*) as count FROM jails;", []);
	
	// Проверяем результат запроса jcounts
	if (isset($jcounts['error']) && $jcounts['error']) {
		$res_array['error'] = true;
		$res_array['error_message'] = $jcounts['info'];
		$included_result_array = $res_array;
		exit;
	}
	
	if (is_array($jcounts) && isset($jcounts['count'])) {
		$res_array['num-jails'] += $jcounts['count'];
	}

	$counts = $ndb->select("SELECT ncpu,physmem,cpufreq FROM local;", []);
	
	// Проверяем результат запроса counts
	if (isset($counts['error']) && $counts['error']) {
		$res_array['error'] = true;
		$res_array['error_message'] = $counts['info'];
		$included_result_array = $res_array;
		exit;
	}
	
	if (is_array($counts)) {
		foreach($counts as $cel){
			if (is_array($cel) && isset($cel['ncpu']) && isset($cel['physmem']) && isset($cel['cpufreq'])) {
				$res_array['num-cores'] += $cel['ncpu'];
				$res_array['sum-ram'] += $cel['physmem'];
				$res_array['average'] += $cel['cpufreq'];
			}
		}
	}
}

if($res_array['average'] > 0){
	$res_array['average'] = $this->GhzConvert($res_array['average']/($res_array['num-nodes']?:1));
}

$res_array['sum-ram'] = $this->fileSizeConvert((int) $res_array['sum-ram'], 1024, true);

$included_result_array = $res_array;