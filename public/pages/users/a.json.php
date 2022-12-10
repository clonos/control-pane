<?php

/*
id INTEGER PRIMARY KEY AUTOINCREMENT,
username VARCHAR(150) UNIQUE NOT NULL, 
password VARCHAR(128) UNIQUE NOT NULL, 
first_name VARCHAR(32), 
last_name VARCHAR(32), 
last_login TIMESTAMP DATE, 
is_active BOOLEAN DEFAULT 'true' NULL, 
date_joined TIMESTAMP DATE DEFAULT (datetime('now','localtime'))  
);
*/

$db = new Db('clonos');
if(!$db->error){
	$res = $db->select("select id,username,first_name,last_name,date_joined,last_login,is_active from auth_user order by date_joined desc", []);
}

$html = '';
$hres = $this->getTableChunk('users','tbody');

foreach($res as $r){
	$html_tpl1 = $hres[1];
	$vars = [
		'id' => $r['id'],
		'login' => $r['username'],
		'first_name' => $r['first_name'],
		'last_name' => $r['last_name'],
		'date_joined' => $r['date_joined'],
		'last_login' => $r['last_login'],
		'is_active' => ($r['is_active']==1) ? 'icon-ok' : '',
		'edit_title' => $this->translate('edit_title'),
		'delete_title' => $this->translate('delete_title'),
	];
	foreach($vars as $var => $val){
		$html_tpl1 = str_replace('#'.$var.'#', $val, $html_tpl1);
	}
	$html .= $html_tpl1;
}

$included_result_array = [
	'tbody' => str_replace(["\n","\r","\t"], '', $html),
	'error' => false,
	'func' => 'fillTable',
	'id' => 'userslist'
];