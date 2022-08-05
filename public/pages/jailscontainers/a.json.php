<?php

if(isset($this->uri_chunks[1])){
	$jail_name = $this->uri_chunks[1];
	include('helpers.json.php');
	return;
}

$db = new Db('base','nodes');
$res = $db->select('select nodename from nodelist', []);
$nodes = ['local'];
foreach($res as $val){
	$nodes[] = $val['nodename'];
}

$html = '';
$html_tpl_1 = '';
$statuses = ['Not Launched','Launched','unknown-1','Maintenance','unknown-3','unknown-4','unknown-5','unknown-6'];
$allnodes = [];
$jail_ids = [];
$nth = 0;
$hres = $this->getTableChunk('jailslist','tbody');

foreach($nodes as $node){
	$db1 = new Db('base', $node);
	if($db1->error !== false){

		$jails = $db1->select("SELECT jname,ip4_addr,status,protected FROM jails WHERE emulator!='bhyve' and hidden!=1 order by jname asc;", []);
		$allnodes[$node] = $jails;
		$num = $nth & 1;

		foreach($jails as $jail){
			if($hres !== false){
				$vnc_port = '';
				$vnc_port_file = $this->workdir.'/jails-system/'.$jail['jname'].'/vnc_port';
				if(file_exists($vnc_port_file)){
					$vnc_port = trim(file_get_contents($vnc_port_file));
				}
				$html_tpl = $hres[1];
				$status = $jail['status'];
				$vars = [
					'nth-num' => 'nth'.$num,
					'node' => $node,
					'ip4_addr' => str_replace(',',',<wbr />', $jail['ip4_addr']),
					'jname' => $jail['jname'],
					'vnc_port' => $vnc_port,
					'vnc_port_status' => 'grey',
					'status' => $status,
					'jstatus' => $this->translate($statuses[$status]),
					'icon' => ($status == 0) ? 'play' : 'stop',
					'desktop' => ($status == 0) ? ' s-off' : ' s-on',
					'maintenance' => ($status == 3) ? ' maintenance' : '',
					'protected' => ($jail['protected'] == 1) ? 'icon-lock' : 'icon-cancel',
					'protitle' => ($jail['protected'] == 1) ? ' title="'.$this->translate('Protected jail').'"' : ' title="'.$this->translate('Delete').'"',
					'vnc_title' => $this->translate('Open VNC'),
					'reboot_title' => $this->translate('Restart jail'),
				];

				foreach($vars as $var => $val){
					$html_tpl = str_replace('#'.$var.'#', $val, $html_tpl);
				}
				if($node != 'local'){
					$html_tpl = str_replace('<span class="icon-cog"></span>', '', $html_tpl);
				}
				$html .= $html_tpl;
			}

			$jail_ids[] = $jail['jname'];

/*
			$jname=$jail['jname'];
			$jail_ids[]=$jname;
			$status=$jail['status'];
			$jstatus=$this->translate($statuses[$status]);
			$icon=($status==0)?'play':'stop';
			$desktop=($status==0)?' s-off':' s-on';
			$maintenance=($status==3)?' maintenance':'';
			//$protected=($jail['protected']==1)?' protected':'';
			$protected=($jail['protected']==1)?'icon-lock':'icon-cancel';
			$protitle=($jail['protected']==1)?' title="'.$this->translate('Protected jail').'"':' title="'.$this->translate('Delete').'"';
			$vnc_title=$this->translate('Open VNC');
			$reboot_title=$this->translate('Restart jail');
			$html.=
<<<EOT
	<tr class="nth{$num}{$desktop}{$maintenance}" id="{$jail['jname']}">
		<td>{$node}</td>
		<td class="txtleft">{$jail['jname']}</td>
		<td class="txtleft jname">{$jail['ip4_addr']}</td>
		<td class="jstatus">{$jstatus}</td>
		<td class="ops" width="5"><span class="icon-cnt"><span class="icon-{$icon}"></span></span></td>
		<td width="5" class="op-settings"><span class="icon-cog"></span></td>
		<td width="5" class="op-reboot" title="{$reboot_title}"><span class="icon-arrows-cw"></span></td>
		<td width="5" class="op-del"{$protitle}><span class="{$protected}"></span></td>
		<td width="5" class="op-vnc"><span class="icon-desktop" title="{$vnc_title}"></span></td>
	</tr>
EOT;
*/
		}
		$nth++;
	} else {
		Utils::clonos_syslog("jailscontainers a.json.php: DB1 FALSE");
	}
}

$tasks = (empty($jail_ids)) ? '' : $this->getRunningTasks($jail_ids);

if($hres !== false){
	$html_tpl_1 = str_replace(["\n","\r","\t"], '', $hres[1]);
	$vars = [
		'nth-num' => 'nth0',
		'status' => '',
		'jstatus' => $this->translate('Creating'),
		'icon' => 'spin6 animate-spin',
		'desktop' => ' s-off',
		'maintenance' => ' maintenance busy',
		'protected' => 'icon-cancel',
		'protitle' => '',
		'vnc_title' => $this->translate('Open VNC'),
		'reboot_title' => $this->translate('Restart jail')
	];

	foreach($vars as $var => $val){
		$html_tpl_1 = str_replace('#'.$var.'#', $val, $html_tpl_1);
	}
}

$protected = [
	0 => [
		'icon' => 'icon-cancel',
		'title' => $this->translate('Delete')
	],
	1 => [
		'icon' => 'icon-lock',
		'title' => $this->translate('Protected jail')
	]
];

$included_result_array = [
	'tbody' => str_replace(["\n","\r","\t"], '', $html),
	'error' => false,
	'func' => 'fillTable',
	'id' => 'jailslist',
	'tasks' => $tasks,
	'template' => $html_tpl_1,
	'protected' => $protected
];